<?php

use App\Consts\AvailableServiceType;
use App\Enums\DemandUpdatesTypeEnum;
use App\Enums\ProcessTypeEnum;
use App\Enums\Status;
use App\Models\Action;
use App\Models\Auth\Profile;
use App\Models\Config\Service;
use App\Models\Config\TransformationType;
use App\Models\DemandAction;
use App\Models\DemandUpdatesHistory;
use App\Models\Order\Demand;
use App\Models\Plate\PlateColor;
use App\Models\Plate\PlateShape;
use App\Models\Vehicle\VehicleCharacteristic;
use App\Models\Vehicle\VehicleCharacteristicCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

function getProfileByPermissions(array $permissions): array|Collection
{
    return Profile::query()
        ->whereHas('roles', function (Builder $query) use ($permissions) {
            $query->whereHas('permissions',function ($query) use ($permissions){
                $query->whereIn('name',$permissions);
            });
        })->get();
}

function getActionToPerformOnDemand(Demand $demand, $postStatus = null): Model|Builder|null
{
    $steps = $demand->service
    ->serviceSteps()
    ->pluck('id')
    ->toArray();

    if(!$postStatus){
        $action = Action::query()
        ->whereIn('service_step_id', $steps)
        ->where('pre_status', $demand->status)
        ->first();
    }else{
        $action = Action::query()
        ->whereIn('service_step_id', $steps)
        ->where('post_status', $postStatus)
        ->first();
    }

    return $action;
}

function getActionToPerformOnDemandByStatus(Demand $demand, string $status): Model|Builder|null
{
    $steps = $demand->service
        ->serviceSteps()
        ->pluck('id')
        ->toArray();

    return Action::query()
        ->whereIn('service_step_id', $steps)
        ->where('post_status', $status)
        ->first();
}

function initProfileActionOnDemand(Demand $demand, string $postStatus = null): Model|Builder|null
{
    $demandAction = null;

    $vehicleTransformationService = Service::query()
        ->where([
            ['id', $demand->service_id],
            ['code', AvailableServiceType::VEHICLE_TRANSFORMATION]])
        ->first();
    if ($vehicleTransformationService && $demand->transformation->grey_card) {
        $action = getActionToPerformOnDemand($demand, Status::closed->name);
    }else{
        //get next action to be performed base on demand current status
        $action = getActionToPerformOnDemand($demand, $postStatus);
    }

    //actionPermission
    if ($action){
        getDemandActionPermission($action);
        //$potentialsProfiles = getProfileByPermissions($permission);
            $demandAction = DemandAction::query()->create([
                'action_id' => $action->id,
                'assigned_at' => now(),
                'demand_id' => $demand->id,
                'done_status' => $action->post_status,
                'profile_id' => $demand->activeTreatment?->responsible_id
            ]);
    }

    return $demandAction;
}

function getDemandActionPermission(Action $action): array
{
    return [$action->permission->permission->name];
}

function updateProfileActionOnDemand(Demand $demand): Model|Builder|null
{
    $demandAction = DemandAction::query()->where([
        'demand_id' => $demand->id,
        'done_status' => $demand->status,
    ])->first();
    $demandAction?->update([
        'done_at' => now(),
         'profile_id' => getOnlineProfile()->id,
         'status' => Status::done->name,
    ]);

    return $demandAction;
}


function shouldAutoProcessStep(Demand $demand, string $status)
{
    return getDemandStepProcessType($demand, $status)->process_type == ProcessTypeEnum::automatic->name;
}

function getDemandStepProcessType(Demand $demand, string $status)
{
   return $demand->service->serviceSteps()->whereRelation('step','status', $status)->first();
}


function getDemandStepByStatus(Demand $demand, string $status)
{
    return $demand->service->serviceSteps()->whereRelation('step','status', $status)->first()->step;
}

function saveDemandUpdateHistory(array $data): Model
{
    $old_value = $data['old_value'];
    $new_value = $data['new_value'];
    $type = $data['type'];

    switch ($type) {
        case DemandUpdatesTypeEnum::plate_color->name:
            $old_value = PlateColor::find($old_value)->label;
            $new_value = PlateColor::find($new_value)->label;
            break;

        case DemandUpdatesTypeEnum::front_plate_shape->name:
            $old_value = PlateShape::find($old_value)->name;
            $new_value = PlateShape::find($new_value)->name;
            break;

        case DemandUpdatesTypeEnum::back_plate_shape->name:
            $old_value = PlateShape::find($old_value)->name;
            $new_value = PlateShape::find($new_value)->name;
            break;

        case DemandUpdatesTypeEnum::vehicle_transformation_type->name:
            $old_value = TransformationType::find($old_value)->label;
            $new_value = TransformationType::find($new_value)->label;
            break;

        case DemandUpdatesTypeEnum::vehicle_characteristic_category->name:
            $old_value = VehicleCharacteristicCategory::find($old_value)->name;
            $new_value = VehicleCharacteristicCategory::find($new_value)->name;
            break;

        case DemandUpdatesTypeEnum::vehicle_characteristic->name:
            $old_value = VehicleCharacteristic::find($old_value)->value;
            $new_value = VehicleCharacteristic::find($new_value)->value;
            break;

        default:
            break;
    }

    $history = DemandUpdatesHistory::query()->create([
        'old_value' => $old_value,
        'new_value' => $new_value,
        'type' => $data['type'],
        'demand_id' => $data['demand_id'],
        'model_type' => $data['model_type'],
        'model_id' => $data['model_id'],
    ]);

    return $history;
}
