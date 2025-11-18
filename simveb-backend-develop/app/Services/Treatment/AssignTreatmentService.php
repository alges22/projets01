<?php

namespace App\Services\Treatment;

use App\Enums\LegalStatusEnum;
use App\Enums\Status;
use App\Exceptions\UnknownServiceException;
use App\Models\Auth\Profile;
use App\Models\Config\ManagementCenter;
use App\Models\Config\Organization;
use App\Models\Order\Demand;
use App\Models\Treatment\Treatment;
use App\Repositories\Crud\CrudRepository;

class AssignTreatmentService
{
    private CrudRepository $treatmentRepository;

    public function __construct()
    {
        $this->treatmentRepository = new CrudRepository(Treatment::class);
    }

    /**
     * @param Demand $demand
     * @return array
     */
    public function assignDemandToCenter(Demand $demand, $center_id = null): array
    {
        $treatment = $demand->activeTreatment;
        $ownerZone = ($demand->vehicleOwner->legal_status === LegalStatusEnum::physical->name)
            ? $demand->vehicleOwner->identity->town->getZone()
            : $demand->vehicleOwner->profile->identity->town->getZone();
        $center = $center_id ? ManagementCenter::find($center_id) : $ownerZone?->getCenter();
        $success = false;

        if ($center) {
            $demand->update([
                'status' => Status::assigned_to_center->name,
            ]);

            $treatment->update([
                'management_center_id' => $center->id,
                'assigned_to_center_at' => now(),
                'assigned_to_center_by' => getOnlineProfile()?->id
            ]);

            //assign demand to a service automatically
            if (shouldAutoProcessStep($demand, Status::assigned_to_service->name)) {
                $this->assignDemandToService($demand);
            }

            $message = "Demande assigné au centre $center->name avec succès";
            $success = true;
        } else {
            logAssigmentError("Center not found", $demand->reference);
            $message = "Echec d'assignation au centre";
        }

        return [
            "demand" => $demand->refresh(),
            "message" => $message,
            "success" => $success,
        ];
    }

    /**
     * @param Demand $demand
     * @return array
     */
    public function assignDemandToService(Demand $demand, $organization_id = null): array
    {
        $success = false;
        $treatment = $demand->activeTreatment;
        $organization = $organization_id ? Organization::find($organization_id) : $demand->service->organization;

        if ($organization) {
            $demand->update([
                'status' => Status::assigned_to_service->name,
            ]);

            $treatment->update([
                'organization_id' => $organization->id,
                'assigned_to_organization_at' => now(),
                'assigned_to_organization_by' => getOnlineProfile()?->id
            ]);

            //assign demand to a staff automatically
            if (shouldAutoProcessStep($demand, Status::assigned_to_staff->name)) {
                $this->autoAssignToStaff($demand);
            }
            $message = "Demande assigné au service $organization->name avec succès";
            $success = true;
        } else {
            logAssigmentError("Center not found", $demand->reference);
            $message = "Echec d'assignation au service";
        }

        return [
            "demand" => $demand->refresh(),
            "message" => $message,
            "success" => $success,
        ];
    }

    public function autoAssignToStaff(Demand $demand): array
    {
        $success = false;
        $candidates = getProfileByPermissions(getDemandActionPermission(getActionToPerformOnDemandByStatus($demand, Status::pre_validated->name)));
        $staff = $candidates->random()->first();
        if ($staff) {
            $this->assignDemandToStaff($demand, $staff);
            $message = "Demande assigné au staff {$staff->identity->fullName} avec succès";
            $success = true;
        } else {
            $message = "Echec d'assignation au staff";
            logAssigmentError("Staff not found", $demand->reference);
        }

        return [
            "demand" => $demand->refresh(),
            "message" => $message,
            "success" => $success
        ];
    }

    public function affectDemandToInterpol(array $data): array
    {
        $demand = Demand::find($data['demand_id']);
        $treatment = $demand->activeTreatment;

        $demand->update([
            'status' => Status::affected_to_interpol->name,
        ]);
        $treatment->update([
            'affected_to_interpol_at' => now(),
            'affected_to_interpol_service_by' => getOnlineProfile()?->id
        ]);

        // INFO: uncomment when assigned_to_interpol_staff will been added to steps
        $interpolStaffProfile = getInterpolStaffToAssignDemand();
        if ($interpolStaffProfile) {
            // if (shouldAutoProcessStep($demand, Status::assigned_to_interpol_staff->name)){
            $this->assignDemandToInterpolStaff(['demand_id' => $demand->id, 'profile_id' => $interpolStaffProfile->id]);
            // }
        }

        // pre validated
        if (shouldAutoProcessStep($demand, Status::pre_validated->name)) {
            $demand->getAdapter()->validate($demand);

            if (shouldAutoProcessStep($demand, Status::validated->name)) {
                $demand->getAdapter()->validate($demand);
            }
        }

        return [
            "demand" => $demand->refresh(),
            "message" => "Demande affecté à interpole avec succès",
            "success" => true
        ];
    }

    public function assignDemandToInterpolStaff(array $data)
    {
        $demand = Demand::find($data['demand_id']);
        $treatment = $demand->activeTreatment;
        $profile = isset($data['profile_id']) ? Profile::query()->find($data['profile_id']) : getInterpolStaffToAssignDemand();

        $treatment->update([
            'interpol_staff_id' => $profile->id,
            'assigned_to_interpol_staff_by' => getOnlineProfile()?->id,
            'assigned_to_interpol_staff_at' => now()
        ]);

        // uncomment if status is added to process steps
        // $demand->update(['status' => Status::assigned_to_interpol_staff->name]);

        return [
            "demand" => $demand->refresh(),
            "message" => "Demande affecté à {$profile->identity->fullName} avec succès",
            "success" => true,
        ];
    }

    /**
     * @throws UnknownServiceException
     */
    public function assignDemandToStaff(Demand $demand, ?Profile $staff): array
    {
        $treatment = $demand->activeTreatment;
        $success = false;

        if ($staff) {
            $treatment->update([
                'responsible_id' => $staff->id,
                'assigned_to_staff_by' => getOnlineProfile()?->id,
                'assigned_to_staff_at' => now()
            ]);
            $demand->update(['status' => Status::assigned_to_staff->name]);
            $success = true;
        }

        if (shouldAutoProcessStep($demand, Status::verified->name)) {
            (new TreatmentService)->verifyTreatment(['treatment_id' => $treatment->id]);
        }

        return [
            "demand" => $demand->refresh(),
            "message" => "Demande assigné à {$staff?->identity?->fullName} avec succès",
            "success" => $success,
        ];
    }
}
