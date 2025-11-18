<?php

namespace App\Repositories;

use App\Enums\ProfileTypesEnum;
use App\Models\Order\Demand;
use App\Models\Treatment\PrintOrder;
use App\Traits\Demands\PrintOrderTrait;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PrintOrderRepository
{
    use PrintOrderTrait;

    public function search(): mixed
    {
        $keyword = strtolower(request('q'));

        $printOrder = null;

        if ($keyword) {
            $printOrder = PrintOrder::whereRaw("LOWER(reference) like ?", ["%$keyword%"])
                ->with(PrintOrder::relations())->first();
        }

        if (!$printOrder) {
            return [Response::HTTP_NOT_FOUND, ['message' => 'Aucun résultat trouvé.']];
        }

        $printOrder->immatriculation = $printOrder->immatriculation;

        return [Response::HTTP_OK, $printOrder];
    }

    public function getAll(bool $paginate = true): mixed
    {
        $onlineProfile = getOnlineProfile();

        $query = PrintOrder::query()
            ->with(PrintOrder::relations())
            ->orderByDesc('created_at')
            ->filter();

        if ($paginate) {
            if ($onlineProfile->type->code == ProfileTypesEnum::user->name) {
                $query->whereHas('immatriculation', function ($query) use ($onlineProfile) {
                    $query->where('vehicle_owner_id', $onlineProfile->id);
                });
            } elseif ($onlineProfile->type->code != ProfileTypesEnum::anatt->name) {
                $query->where('institution_id', $onlineProfile->institution->id);
            }

            return $query->paginate(request('per_page', '15'));
        }

        return $query->get();
    }


    public function store(array $data)
    {
        DB::beginTransaction();
        try {

            $emitResult = $this->emitPrintOrder(Demand::find($data['demand_id']));

            DB::commit();

            return $emitResult instanceof PrintOrder ? [
                'success' => true,
                'data' => [
                    'reference' => $emitResult->reference,
                    'message' => 'Ordre d\'impression émis avec succès.'
                ],
            ] : [
                'success' => false,
                'message' => $emitResult,
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY
            ];
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }
}
