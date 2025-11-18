<?php

namespace App\Repositories;

use App\Enums\Status;
use App\Models\Config\PaymentProvider;
use App\Repositories\Crud\AbstractCrudRepository;
use Exception;
use App\Traits\UserDataTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class PaymentProviderRepository extends AbstractCrudRepository
{
    use UserDataTrait;

    public function __construct()
    {
        parent::__construct(PaymentProvider::class);
    }

    public function getAll(bool $paginate = true,$relations = []): mixed
    {
        return $this->model->newQuery()
            ->with([
                'activator',
                'deactivator',
                'author',
                'activator.identity',
                'deactivator.identity',
                'author.identity'
            ])
            ->orderByDesc('created_at')
            ->filter()
            ->paginate();
    }

    public function getActive(bool $paginate = true,$relations = []): mixed
    {
        return $this->model->newQuery()
            ->where('is_active', true)
            ->with([
                'activator',
                'deactivator',
                'author'
            ])
            ->orderByDesc('created_at')
            ->filter()
            ->paginate();
    }

    public function update($paymentProvider, array $data, $request = null): Model
    {
        DB::beginTransaction();
        try {
            
            $paymentProvider = parent::update($paymentProvider, $data, $request);

            DB::commit();
            $paymentProvider->load($paymentProvider::relations())->refresh();
            return $paymentProvider;
        } catch (Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, __('errors.server_error'));
        }
    }

    public function toggle($paymentProvider): mixed
    {
        DB::beginTransaction();
        try {
            if ($paymentProvider->is_active) {
                $paymentProvider->update([
                    'is_active' => false,
                    'deactivated_at' => now(),
                    'deactivator_id' => getOnlineProfile()?->id
                ]);
            } else {
                $paymentProvider->update([
                    'is_active' => true,
                    'activated_at' => now(),
                    'activator_id' => getOnlineProfile()?->id
                ]);
            }
            
            DB::commit();
            $paymentProvider->load($paymentProvider::relations())->refresh();
            
            return $paymentProvider;
        } catch (Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, __('errors.server_error'));
        }
    }

    public function default($paymentProvider): mixed
    {
        DB::beginTransaction();
        try {
            $paymentProvider->update([
                'is_default' => true,
            ]);
            PaymentProvider::whereNot('id', $paymentProvider->id)->update(['is_default' => false,]);

            DB::commit();
            $paymentProvider->load($paymentProvider::relations())->refresh();

            return $paymentProvider;
        } catch (Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, __('errors.server_error'));
        }
    }
}
