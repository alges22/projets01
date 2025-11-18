<?php

namespace App\Repositories\Demand;

use App\Exports\OrdersExport;
use App\Models\Institution\Institution;
use App\Models\Order\Order;
use App\Services\GeneratePdfService;
use App\Repositories\Crud\CrudRepositoryHandler;
use Illuminate\Support\Str;

class OrderRepository extends CrudRepositoryHandler
{
    public function __construct()
    {
        parent::__construct(Order::class);
    }


    public function getClientOrders()
    {
        if (getOnlineProfile()->isUserProfile()) {
            $query = [
                'profile_id' => getOnlineProfile()->id
            ];
        } else {
            $query = [
                'institution_id' => getOnlineProfile()->institution_id
            ];
        }

        $query =  $this->model->newQuery()
            ->filter()
            ->orderByDesc('orders.created_at')
            ->select([
                'id',
                'profile_id',
                'institution_id',
                'amount',
                'created_at',
                'status'
            ])
            ->with([
                'demands:id,reference,service_id,model_id,model_type' => [
                    'service:id,name,type_id' => ['type:id,code']
                ],
                'transaction:id,reference,status,fees,amount,total_amount,model_id,model_type'
            ])
            ->where($query);
        return (bool) request()->input('paginate') == 1 ?
            $query->paginate(request()->input('per_page', 15)) : $query->get();
    }

    public function getClientOrder(Order $order)
    {
        return $order->load([
            'demands:id,reference,service_id,model_id,model_type' => [
                'service:id,name,type_id' => ['type:id,code']
            ],
            'transaction:id,reference,status,fees,amount,total_amount,model_id,model_type',
            'invoice:id,model_id,model_type,reference',
        ]);
    }

    public function getOrders()
    {
        if (getOnlineProfile()->isUserProfile()) {
            $query = [
                'profile_id' => getOnlineProfile()->id
            ];
        } else {
            $anatt = Institution::where('acronym', 'ANaTT')->first();
            if ($anatt->id == getOnlineProfile()->institution_id) {
                $query = [];
            } else {
                $query = [
                    'institution_id' => getOnlineProfile()->institution_id
                ];
            }
        }

        $query =  $this->model->newQuery()
            ->filter()
            ->select([
                'id',
                'profile_id',
                'reference',
                'institution_id',
                'amount',
                'created_at',
                'status'
            ])
            ->with([
                'demands:id,reference,service_id,model_id,model_type' => [
                    'service:id,name,type_id' => ['type:id,code']
                ],
                'transaction:id,reference,status,fees,amount,total_amount,model_id,model_type',
                'profile',
                'profile.identity',
                'institution'
            ])
            ->where($query);
        return $query->orderByDesc('orders.created_at');
    }

    public function getOrder(Order $order)
    {
        return $order->load([
            'demands:id,reference,service_id,model_id,model_type' => [
                'service:id,name,type_id' => ['type:id,code']
            ],
            'profile',
            'institution',
            'transaction:id,reference,status,fees,amount,total_amount,model_id,model_type,payment_provider_id' => ['paymentProvider'],
            'invoice:id,model_id,model_type,reference',
        ]);
    }

    /**
     *
     */
    public function getOrderStats()
    {
        $query =  $this->model->newQuery()
            ->filter();

        return [
            'orders_total' => $query->get()->count(),
            'status_counts' => $query->select('status', \DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray(),
            'status_data' => $query->sum('amount')
        ];
    }

    /**
     *
     */
    public function excelExport()
    {
        $query = $this->getOrders();
        return (new OrdersExport($query))->download('orders.xlsx');
    }

    /**
     *
     */
    public function pdfExport()
    {
        $query = $this->getOrders();
        return GeneratePdfService::generatePDF(
            view: 'exports.pdf.orders',
            data: ['orders' => $query->get()],
            filename: 'orders_' . now()->timestamp . '_' . Str::random(3) . '.pdf',
            folder: 'orders/' . Str::snake(class_basename($this)) . '_orders',
            stream: true,
        );
    }
}
