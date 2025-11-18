<?php

namespace App\Services;

use App\Enums\Status;
use App\Models\Order\Order;
use App\Repositories\Crud\CrudRepository;
use App\Services\Demand\DemandService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class OrderService
{
    private CrudRepository $repository;
    private readonly DemandService $demandeService;

    public function __construct()
    {
        $this->repository = new CrudRepository(Order::class);
        $this->demandeService = new DemandService;
    }

    public function submitOrder(string $orderId, string $paymentRef)
    {
        DB::beginTransaction();
        try {
            $order = $this->repository->find($orderId);
            foreach ($order->demands as $demand) {
                $this->demandeService->submitDemand($demand);
            }
            $order->update([
                'payment_status' => Status::approved->name,
                'paid_at' => now(),
                'submitted_at' => now(),
            ]);
            $order->transaction->update([
                'payment_reference' => $paymentRef,
                'status' => Status::approved->name,
            ]);

            $order->invoice()->create([
                'amount' => $order->amount,
                'status' => Status::approved->name,
                'paid_at' => now(),
                'institution_id' => $order->institution_id,
                'profile_id	' => $order->profile_id	,
            ]);
            $order->update([
                'status' => Status::approved
            ]);
            emptyCart();

            DB::commit();
            return $order->load(['invoice']);
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
