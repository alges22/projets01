 <?php

namespace App\Repositories\Immatriculation;

use App\Consts\NotificationNames;
use App\Consts\Roles;
use App\Enums\Status;
use App\Models\Order\Demand;
use App\Notifications\NotificationSender;
use App\Repositories\Crud\AbstractCrudRepository;
use App\Traits\UserDataTrait;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ImmatriculationDemandRepository extends AbstractCrudRepository
{
    use UserDataTrait;

    public function __construct()
    {
        parent::__construct(Demand::class);
    }



    public function anattValidation(array $data)
    {
        try {
            DB::beginTransaction();

            $immatriculationDemand = Demand::findOrFail($data['immatriculation_demand_id']);

            $immatriculationDemand->update([
                'status' => $data['action'] == 'validate' ? Status::validated_by_anatt->name : Status::rejected_by_anatt->name,
            ]);

            $immatriculationDemand->activeTreatment->update([
                'validated_by_anatt_at' => $data['action'] == 'validate' ? now() : null,
                'rejected_by_anatt_at' => $data['action'] == 'reject' ? now() : null,
                'anatt_observations' => $data['observations'],
            ]);

            sendMail(
                null,
                $immatriculationDemand->vehicleOwner->identity,
                $data['action'] == 'validate' ? NotificationNames::ANATT_VALIDATE_PLATE : NotificationNames::ANATT_REJECT_PLATE,
                ['reference' => $immatriculationDemand->reference]
            );
            DB::commit();

            return $immatriculationDemand->load(Demand::relations());
        } catch (Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, 'Oups! Une erreur est survenue');
        }
    }

}
