<?php

namespace App\Repositories\Plate;

use App\Consts\NotificationNames;
use App\Enums\ProfileTypesEnum;
use App\Enums\Status;
use App\Enums\TransactionTypesEnum;
use App\Jobs\ImportOrderedPlatesJob;
use App\Models\Order\Invoice;
use App\Models\Plate\PlateColor;
use App\Models\Plate\PlateOrder;
use App\Models\Plate\PlateShape;
use App\Traits\UploadFile;
use App\Traits\UserDataTrait;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class PlateOrderRepository
{
    use UserDataTrait, UploadFile;

    private $class;

    public function __construct()
    {
        $this->class = PlateOrder::class;
    }

    public function myOrders(bool $paginate = true)
    {
        $query = $this->class::where('buyer_id', getOnlineProfile()->institution_id)
            ->with(['seller:id,name,email,telephone'])
            ->select(['id', 'buyer_id', 'seller_id', 'reference', 'created_at', 'reference', 'amount', 'status', 'author_profile_id'])
            ->orderByDesc('created_at')->filter();

        return $paginate ? $query->paginate(request('per_page', 15)) : $query->get();
    }

    public function invoices(bool $paginate = true)
    {
        $plateOrdersId = $this->class::where('buyer_id', getOnlineProfile()->institution_id)->pluck('id')->toArray();

        $query = Invoice::where('model_type', $this->class)->whereIn('model_id', $plateOrdersId)
            ->orderByDesc('created_at');

        return $paginate ? $query->paginate(request('per_page', 15)) : $query->get();
    }

    public function orderRequests(bool $paginate = true)
    {
        $query = $this->class::filter()
            ->with(['buyer:id,name,email,telephone'])
            ->select(['id', 'buyer_id', 'seller_id', 'reference', 'created_at', 'reference', 'amount', 'status', 'author_profile_id'])
            ->orderByDesc('created_at');

        $onlineProfile = getOnlineProfile();

        if ($onlineProfile->type->code == ProfileTypesEnum::anatt->name) {
            $query->where('seller_id', null);
        } else {
            $query->where('seller_id', $onlineProfile->institution_id);
        }

        return $paginate ? $query->paginate(request('per_page', 15)) : $query->get();
    }

    public function store(array $data)
    {
        $data['amount'] = 0;
        $data['order_data'] = [];
        $data['quantity'] = 0;

        foreach (Arr::pull($data, 'plates') as $plateData) {
            $plateShape = PlateShape::findOrFail($plateData['plate_shape_id']);
            $plateColor = PlateColor::findOrFail($plateData['plate_color_id']);
            $data['amount'] += (($plateShape->cost + $plateColor->cost) * $plateData['nb']);
            $data['quantity'] += $plateData['nb'];

            $data['order_data'][] = $plateData;
        }

        $onlineProfile = getOnlineProfile();

        $data['reference'] = $this->generateUniqueReference();
        $data['author_profile_id'] = $onlineProfile->id;
        $data['buyer_id'] = $onlineProfile->type->code == ProfileTypesEnum::anatt->name ? null : $onlineProfile->institution_id;

        $plateOrder = $this->class::create($data);

        if ($onlineProfile->type->code == ProfileTypesEnum::anatt->name) {
            // TODO: notify seller
        } else {
            // TODO: notify anatt
        }

        return $plateOrder->load($this->class::relations());
    }

    public function confirmOrder(array $data)
    {
        $plateOrder = $this->class::findOrFail($data['plate_order_id']);

        try {
            DB::beginTransaction();

            $filePath = $this->saveFile($data['file'], 'to-import/plate-orders');

            $plateOrder->update([
                'validation_file' => $filePath['path'],
                'confirmator_id' => getOnlineProfile()->id,
                'confirmed_at' => now(),
                'status' => Status::waiting_for_payment->name,
            ]);

            if (empty($plateOrder->buyer_id)) {
                sendMail(
                    null,
                    $plateOrder->seller,
                    NotificationNames::PLATE_ORDER_CONFIRMED,
                    ['reference' => $plateOrder->reference]
                );
            } else {
                // TODO: send notification to one anatt member mail
                /* sendMail(
                    null,
                    $plateOrder->seller,
                    NotificationNames::PLATE_ORDER_PAYMENT_PENDING,
                    ['reference' => $plateOrder->reference]
                ); */
            }

            DB::commit();

            return [true, ['message' => "Commande approuvée."]];
        } catch (Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, 'Oups! Une erreur est survenue');
        }
    }

    public function rejectOrder(array $data)
    {
        $plateOrder = $this->class::findOrFail($data['plate_order_id']);

        $plateOrder->update([
            'status' => Status::rejected->name,
            'rejected_at' => now(),
            'rejector_id' => getOnlineProfile()->id,
            'rejected_reason' => $data['reason'],
        ]);

        return [true, $plateOrder];
    }

    public function generateUniqueReference()
    {
        $reference = generateReference('PO-');

        return PlateOrder::where('reference', $reference)->exists() ? $this->generateUniqueReference() : $reference;
    }

    public function payOrder(array $data)
    {
        DB::beginTransaction();
        try {
            $plateOrder = PlateOrder::find($data['plate_order_id']);

            $wallet = getOnlineProfile()->space->wallet;

            $wallet->transactions()->create([
                'type' => TransactionTypesEnum::debit->name,
                'amount' => $plateOrder->amount,
                'total_amount' => $plateOrder->amount,
                'status' => Status::validated->name,
            ]);

            $oldBalance = $wallet->balance;

            $wallet->update(['balance' => $oldBalance - $plateOrder->amount]);

            $plateOrder->update([
                'status' => Status::paid->name,
                'paid_at' => now(),
                'payment_status' => Status::validated->name,
            ]);

            sendNotification(
                NotificationNames::PLATE_ORDER_PAYMENT_SUCCESS,
                $plateOrder->buyer,
                ['reference' => $plateOrder->reference],
                ['mail']
            );

            DB::commit();
            return $plateOrder;
        } catch (Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, 'Oups! Une erreur est survenue');
        }
    }

    public function validateOrder(array $data)
    {
        $plateOrder = $this->class::findOrFail($data['plate_order_id']);

        DB::beginTransaction();
        try {
            $filePath = $this->saveFile($data['file'], 'delivery-slips');

            $plateOrder->update([
                'delivery_slip_file' => $filePath['path'],
                'status' => Status::validated->name,
                'delivered_at' => now(),
                'validator_id' => getOnlineProfile()->id,
                'validated_at' => now(),
            ]);

            $plateOrder->invoice()->create([
                'amount' => $plateOrder->amount,
                'status' => Status::approved->name,
                'paid_at' => now(),
                'institution_id' => $plateOrder->buyer_id,
                'profile_id' => getOnlineProfile()->id,
            ]);

            ImportOrderedPlatesJob::dispatch($plateOrder->validation_file, $plateOrder);

            DB::commit();
            return $plateOrder;
        } catch (Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, 'Oups! Une erreur est survenue');
        }
    }
}
