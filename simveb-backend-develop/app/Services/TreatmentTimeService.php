<?php

namespace App\Services;

use App\Enums\Status;
use App\Models\Config\TreatmentTime;
use App\Models\Treatment\Treatment;
use App\Repositories\Crud\CrudRepository;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

/**
 * TreatmentTimeService
 */
class TreatmentTimeService
{
    private CrudRepository $treatmentTimeRepository;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->treatmentTimeRepository = new CrudRepository(TreatmentTime::class);
    }


    /**

     * start
     * @param Treatment $treatment
     * @param string $status The status of the processing (e.g. Status::submitted->name, Status::verified->name, etc.).
     * @return Model The instance of the newly created treatment time, or null if the treatment does not exist.
     *
     */
    public function startTreatmentTime(Treatment $treatment, string $status): Model
    {
        DB::beginTransaction();
        try {
            $data = [
                'start_at' => now(),
                'profile_id' => getOnlineProfile()?->id,
                'treatment_id' => $treatment->id,
                'status' => $status,
                'end_at' => $status == Status::closed->name ? now() : null,
            ];

            if ($lastTreatmentTime = $this->treatmentTimeRepository
                ->model->newQuery()
                ->where('treatment_id', $treatment->id)
                ->where('status', '!=', $status)
                ->latest()->first()
            ) {
                $lastTreatmentTime->update(['end_at' => now()]);
            }

            $treatmentTime = TreatmentTime::query()->where([
                'profile_id' => getOnlineProfile()?->id,
                'treatment_id' => $treatment->id,
                'status' => $status
            ])->first();

            if ($treatmentTime == null) {
                $treatmentTime = TreatmentTime::query()->create($data);
            }

            DB::commit();
            return $treatmentTime->refresh();
        } catch (Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, __('errors.server_error'));
        }
    }
}
