<?php

namespace App\Services\Treatment;

use App\Consts\AvailableServiceType;
use App\Consts\NotificationNames;
use App\Enums\Status;
use App\Events\NewDemandSubmittedEvent;
use App\Exceptions\UnknownOpertionException;
use App\Models\Config\ManagementCenter;
use App\Models\Config\Organization;
use App\Models\Immatriculation\ImmatriculationLabel;
use App\Models\Order\Demand;
use App\Models\Treatment\Treatment;
use App\Notifications\NotificationSender;
use App\Repositories\Crud\CrudRepository;
use App\Repositories\Vehicle\VehicleRepository;
use App\Services\GrayCardDuplicate;
use App\Services\Immatriculation\ImmatriculationNumberService;
use App\Traits\CrudRepositoryTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;

class TreatmentService
{

    private readonly CrudRepository $treatmentRepository;
    private readonly CrudRepository $demandRepository;
    private ImmatriculationNumberService $immatriculationNumberService;
    private VehicleRepository $vehicleRepository;
    private AssignTreatmentService $assignmentService;

    use CrudRepositoryTrait;

    public function __construct()
    {
        $this->treatmentRepository = new CrudRepository(Treatment::class);
        $this->demandRepository = new CrudRepository(Demand::class);
        $this->immatriculationNumberService = new ImmatriculationNumberService;
        $this->initRepository(ImmatriculationLabel::class);
        $this->vehicleRepository = new VehicleRepository;
        $this->assignmentService = new AssignTreatmentService;
    }

    public function create()
    {
        return [
            'organizations' => Organization::query()->select(['name','id'])->get(),
            'centers' => ManagementCenter::query()->select(['name','id'])->get(),
        ];
    }


    public function printImmatriculation($data)
    {
        $treatment = $this->treatmentRepository->find($data['treatment_id']);
        $demand = $treatment->model;


        if ($treatment->print_order_emitted_at){
            $treatment->update([
                'printed_by' => Auth::id() ?? null,
                'printed_at' => now(),
                'print_observations' => $data['print_observations'] ?? null
            ]);
            $demand->update(['status' => Status::print_order_emitted->name]);
        }else{
            $treatment->update([
                'print_order_emitted_by' => Auth::id() ?? null,
                'print_order_emitted_at' => now(),
            ]);

            $demand->update(['status' => Status::print_order_emitted->name]);
        }
        Notification::send($demand->vehicleOwner->identity, new NotificationSender(NotificationNames::IMMATRICULATION_PRINT_ORDER_EMITTED, ['mail'], ['reference' => $demand->reference]));

        //Notification::send($user->identity,new NotificationSender(NotificationNames::IMMATRICULATION_ASSIGNED_TO_SERVICE));

        return $demand->load($demand::relations())->refresh();
    }


    /**
     * @param Demand $demand
     * @return Demand
     */
    public function submitDemand(Demand $demand)
    {
        $treatment  = $demand->treatments()->create();
        $demand->update([
            'status' => Status::submitted->name,
            'submitted_at' => now(),
            'active_treatment_id' => $treatment->id
        ]);

        $this->assignmentService->assignDemandToService($demand);
        Event::dispatch(new NewDemandSubmittedEvent($demand));

        return $demand->refresh();
    }


    /**
     * @throws \Exception
     */
    public function verifyTreatment(array $data)
    {

        $treatment = $this->treatmentRepository->find($data['treatment_id']);
        $demand = $treatment->demand;

        $treatment->update([
            'verified_by' => getOnlineProfile()?->id,
            'verified_at' => now(),
            'status' => Status::verified->name
        ]);
        $demand->update(['status' => Status::verified->name]);

        // determine next acction
        $nextStatus = match ($demand->service->type->code) {
            AvailableServiceType::IMMATRICULATION_STANDARD,
            AvailableServiceType::IMMATRICULATION_PRESTIGE_LABEL,
            AvailableServiceType::IMMATRICULATION_PRESTIGE_NUMBER,
            AvailableServiceType::IMMATRICULATION_PRESTIGE_NUMBER_LABEL,
            AvailableServiceType::RE_IMMATRICULATION => Status::affected_to_interpol->name,
            default => Status::pre_validated->name,
        };

        // check auto assign
        if (shouldAutoProcessStep($demand, $nextStatus)) {
            if ($nextStatus == Status::affected_to_interpol->name) {
                (new AssignTreatmentService)->affectDemandToInterpol(['demand_id' => $demand->id]);
            } else {
                // pre validated
                $demand->getAdapter()->validate($demand);

                if (shouldAutoProcessStep($demand, Status::validated->name)) {
                    $demand->getAdapter()->validate($demand);
                }
            }
        }

        return [
            "success" => true,
            "message" => "Demande vérifiée avec succès.",
            "demand" => $demand->load($demand::relations())->refresh()
        ];
    }

    public function rejectTreatment(array $data)
    {
        $treatment = $this->treatmentRepository->find($data['treatment_id']);
        $model = $treatment->model;

        $treatment->update([
            'rejected_by' => Auth::id() ?? null,
            'rejected_at' => now(),
            'rejected_reason' => $data['reason']
        ]);
        $model->update(['status' => Status::rejected->name]);

        return $model->refresh();
    }

    public function suspendTreatment(array $data)
    {
        $treatment = $this->treatmentRepository->find($data['treatment_id']);
        $model = $treatment->model;

        $treatment->update([
            'suspended_by' => Auth::id() ?? null,
            'suspended_at' => now(),
            'suspended_reason' => $data['reason']
        ]);

        $model->update(['status' => Status::suspended->name]);

        return $model->refresh();
    }


}
