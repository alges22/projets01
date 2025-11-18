<?php

namespace Database\Seeders\Config;

use App\Consts\AvailableServiceType;
use App\Enums\ProcessTypeEnum;
use App\Enums\Status;
use App\Exceptions\UnknownServiceException;
use App\Models\Action;
use App\Models\Auth\Profile;
use App\Models\Config\Service;
use App\Models\Config\ServiceType;
use App\Models\Config\Organization;
use App\Models\Config\Step;
use App\Models\ServiceStep;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Ntech\RequiredDocumentPackage\Models\DocumentType;
use Ntech\UserPackage\Database\Seeders\Modules\ServicesModule;
use Spatie\Permission\Models\Permission;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $documentTypeId  = DocumentType::pluck('id')->take(2)->toArray();

        foreach (ServiceType::query()->get() as $serviceType) {
            $service = Service::query()->updateOrCreate(
                [
//                    'code' => $serviceType->code,
                    'name' => $serviceType->name,
                ],
                [
                    'type_id' => $serviceType->id,
                    'is_child' => false,
                    //'is_active' => false,
                    'target_organization_id' => Organization::query()->first()?->id,
                    'name' => Str::replace('_', ' ', $serviceType->name),
                    'description' => $serviceType->description,
                    'duration' => 24,
                    'cost' => 20000,
                    'procedures' => '-Procédure',
                    'who_can_apply' => 'Tout le monde',
                    'link' => null,
                    'status' => 'validated',
                    //'published' => false,
                    'extract' => 'Extrait',
                    'code' => $serviceType->code,
                ]
            );
            $service->load(['steps', 'documents']);
            $service->documents()->sync($documentTypeId);
            $index = 1;
            switch ($service->type->code) {
                case AvailableServiceType::IMMATRICULATION_STANDARD:
                case AvailableServiceType::IMMATRICULATION_PRESTIGE_NUMBER:
                case AvailableServiceType::IMMATRICULATION_PRESTIGE_LABEL:
                case AvailableServiceType::IMMATRICULATION_PRESTIGE_NUMBER_LABEL:
                case AvailableServiceType::RE_IMMATRICULATION:
                    foreach (Step::query()->get() as $step) {
                        ServiceStep::updateOrCreate(
                            [
                                'service_id' => $service->id,
                                'step_id' => $step->id,
                                'position' => $index,
                            ],
                            [
                                'duration' => 3600,
                                'process_type' => ProcessTypeEnum::manual->name,
                            ]
                        );
                        $index++;
                    }
                    break;
                case AvailableServiceType::GRAY_CARD_DUPLICATE:
                case AvailableServiceType::PLATE_DUPLICATE:
                case AvailableServiceType::PLATE_TRANSFORMATION:
                case AvailableServiceType::VEHICLE_TRANSFORMATION:
                    foreach (Step::query()->whereNotIn('status', [Status::affected_to_interpol->name])->get() as $step) {
                        ServiceStep::updateOrCreate(
                            [
                                'service_id' => $service->id,
                                'step_id' => $step->id,
                                'position' => $index,
                            ],
                            [
                                'duration' => 3600,
                                'process_type' => ProcessTypeEnum::manual->name,
                            ]
                        );
                        $index++;
                    }
                    break;
                default:
                    foreach (
                        Step::query()->whereNotIn('status', [
                            Status::affected_to_interpol->name,
                            Status::print_order_emitted->name,
                            Status::print_order_validated->name
                        ])->get()
                        as
                        $step
                    ) {
                        ServiceStep::updateOrCreate(
                            [
                                'service_id' => $service->id,
                                'step_id' => $step->id,
                                'position' => $index,
                            ],
                            [
                                'duration' => 3600,
                                'process_type' => ProcessTypeEnum::manual->name,
                            ]
                        );
                        $index++;
                    };
            }

            foreach (ServicesModule::PERMISSIONS as $value) {
                if ($value['code'] == $service->code || $value['code'] == ServicesModule::GLOBAL_SERVICE) {
                    foreach ($value['permissions'] as $permission) {
                        $permission = Permission::where('name', $permission['name'])->first();
                        if (!$service->servicePermissions()->where('permission_id', $permission->id)?->first()) {
                            $service->servicePermissions()->attach($permission->id, ['id' => Str::uuid()]);
                        }
                    }
                }
            }

            $this->affectChildren($service);
            if ($service->servicePermissions->count() > 0) {
                $this->createAction($service);
            }
        }
    }

    private function affectChildren($service): void
    {
        $code = $service->type->code;
        if ($code == AvailableServiceType::IMMATRICULATION) {
            $service->children()
                ->sync(Service::query()->whereIn('code', [
                    AvailableServiceType::IMMATRICULATION_STANDARD,
                    AvailableServiceType::IMMATRICULATION_PRESTIGE_LABEL,
                    AvailableServiceType::IMMATRICULATION_PRESTIGE_NUMBER_LABEL,
                    AvailableServiceType::IMMATRICULATION_PRESTIGE_NUMBER,
                ])->pluck('id')->toArray());
            $service->children()->update(['is_child' => true]);
        } elseif ($code == AvailableServiceType::TITLE_DEPOSIT_OR_RECOVERY) {
            $service->children()
                ->sync(Service::query()->whereIn('code', [
                    AvailableServiceType::TITLE_DEPOSIT,
                    AvailableServiceType::TITLE_RECOVERY,
                ])->pluck('id')->toArray());
            $service->children()->update(['is_child' => true]);
        }
    }

    public function createAction($service)
    {
        $i = 1;
        foreach ($service->serviceSteps as $step) {
            switch ($step->step?->status) {
                case Status::submitted->name:
                    $permissionService = $service->servicePermissions()->where('name', 'like', "assign-to-center")->first();
                    if ($permissionService) {
                        Action::updateOrCreate(
                            [
                                'service_step_id' => $step->id,
                                'permission_service_id' => $permissionService->pivot->id,
                                'duration' => $step->duration,
                            ],
                            [
                                'process_type' => $step->process_type,
                                'author_id' => Profile::first()->id,
                                'position' => $i,
                                'pre_status' => Status::submitted->name,
                                'post_status' => Status::assigned_to_center->name,
                                'description' => Permission::find($permissionService?->pivot->permission_id)?->label,
                            ]
                        );
                        $i++;
                    }
                    break;
                case Status::assigned_to_center->name:
                    $permissionService = $service->servicePermissions()->where('name', 'like', "assign-to-service")->first();
                    if ($permissionService) {
                        Action::updateOrCreate(
                            [
                                'service_step_id' => $step->id,
                                'permission_service_id' => $permissionService->pivot->id,
                                'duration' => $step->duration,
                            ],
                            [
                                'process_type' => $step->process_type,
                                'author_id' => Profile::first()->id,
                                'position' => $i,
                                'pre_status' => Status::assigned_to_center->name,
                                'post_status' => Status::assigned_to_service->name,
                                'description' => Permission::find($permissionService?->pivot->permission_id)?->label,
                            ]
                        );
                        $i++;
                    }
                    break;
                case Status::assigned_to_service->name:
                    $permissionService = $service->servicePermissions()->where('name', 'like', "assign-to-staff")->first();
                    if ($permissionService) {
                        Action::updateOrCreate(
                            [
                                'service_step_id' => $step->id,
                                'permission_service_id' => $permissionService->pivot->id,
                                'duration' => $step->duration,
                            ],
                            [
                                'process_type' => $step->process_type,
                                'author_id' => Profile::first()->id,
                                'position' => $i,
                                'pre_status' => Status::assigned_to_service->name,
                                'post_status' => Status::assigned_to_staff->name,
                                'description' => Permission::find($permissionService?->pivot->permission_id)?->label,
                            ]
                        );
                        $i++;
                    }

                    break;
                case Status::assigned_to_staff->name:
                    $permissionService = $service->servicePermissions()->where('name', 'like', "verify-%")->first();
                    if ($permissionService) {
                        Action::updateOrCreate(
                            [
                                'service_step_id' => $step->id,
                                'permission_service_id' => $permissionService->pivot->id,
                                'duration' => $step->duration,
                            ],
                            [
                                'process_type' => $step->process_type,
                                'author_id' => Profile::first()->id,
                                'position' => $i,
                                'pre_status' => Status::assigned_to_staff->name,
                                'post_status' => Status::verified->name,
                                'description' => Permission::find($permissionService?->pivot->permission_id)?->label,
                            ]
                        );
                        $i++;
                    }
                    break;
                case Status::verified->name:
                    $permissionService = $service->servicePermissions()->where('name', 'like', "affect-to-interpol")->first();
                    if ($permissionService) {
                        Action::updateOrCreate(
                            [
                                'service_step_id' => $step->id,
                                'permission_service_id' => $permissionService->pivot->id,
                                'duration' => $step->duration,
                            ],
                            [
                                'process_type' => $step->process_type,
                                'author_id' => Profile::first()->id,
                                'pre_status' => Status::verified->name,
                                'post_status' => match ($service->type->code) {
                                    AvailableServiceType::IMMATRICULATION_STANDARD,
                                    AvailableServiceType::IMMATRICULATION_PRESTIGE_LABEL,
                                    AvailableServiceType::IMMATRICULATION_PRESTIGE_NUMBER,
                                    AvailableServiceType::IMMATRICULATION_PRESTIGE_NUMBER_LABEL,
                                    AvailableServiceType::RE_IMMATRICULATION => Status::affected_to_interpol->name,
                                    default => Status::pre_validated->name,
                                },
                                'position' => $i,
                                'description' => Permission::find($permissionService?->pivot->permission_id)?->label,
                            ]
                        );
                        $i++;
                    }

                    break;
                case Status::affected_to_interpol->name:
                    $permissionService = $service->servicePermissions()->where('name', 'like', "pre-validate-%")->first();
                    if ($permissionService) {
                        Action::updateOrCreate(
                            [
                                'service_step_id' => $step->id,
                                'permission_service_id' => $permissionService->pivot->id,
                                'duration' => $step->duration,
                            ],
                            [
                                'process_type' => $step->process_type,
                                'author_id' => Profile::first()->id,
                                'position' => $i,
                                'pre_status' => Status::affected_to_interpol->name,
                                'post_status' => Status::pre_validated->name,
                                'description' => Permission::find($permissionService?->pivot->permission_id)?->label,
                            ]
                        );
                        $i++;
                    }
                    break;
                case Status::pre_validated->name:
                    $permissionService = $service->servicePermissions()->where('name', 'like', "validate-%")->first();
                    if ($permissionService) {
                        Action::updateOrCreate(
                            [
                                'service_step_id' => $step->id,
                                'permission_service_id' => $permissionService->pivot->id,
                                'duration' => $step->duration,
                            ],
                            [
                                'process_type' => $step->process_type,
                                'author_id' => Profile::first()->id,
                                'position' => $i,
                                'pre_status' => Status::pre_validated->name,
                                'post_status' => Status::validated->name,
                                'description' => Permission::find($permissionService?->pivot->permission_id)?->label,
                            ]
                        );
                        $i++;
                    }
                    break;
                case Status::print_order_emitted->name:
                    $permissionService = $service->servicePermissions()->where('name', 'like', "print-%")->first();
                    if ($permissionService) {
                        Action::updateOrCreate(
                            [
                                'service_step_id' => $step->id,
                                'permission_service_id' => $permissionService->pivot->id,
                            ],
                            [
                                'process_type' => $step->process_type,
                                'duration' => $step->duration,
                                'author_id' => Profile::first()->id,
                                'position' => $i,
                                'pre_status' => Status::print_order_emitted->name,
                                'post_status' => Status::print_order_validated->name,
                                'description' => Permission::find($permissionService?->pivot->permission_id)?->label,
                            ]
                        );
                        $i++;
                    }
                    break;
                case Status::print_order_validated->name:
                    $permissionService = $service->servicePermissions()->where('name', 'like', "print-%")->first();
                    if ($permissionService) {
                        Action::updateOrCreate(
                            [
                                'service_step_id' => $step->id,
                                'permission_service_id' => $permissionService->pivot->id,
                            ],
                            [
                                'process_type' => $step->process_type,
                                'duration' => $step->duration,
                                'author_id' => Profile::first()->id,
                                'position' => $i,
                                'pre_status' => Status::print_order_validated->name,
                                'post_status' => Status::closed->name,
                                'description' => Permission::find($permissionService?->pivot->permission_id)?->label,
                            ]
                        );
                        $i++;
                    }
                    break;
                case Status::validated->name:
                    $permissionService = $service->servicePermissions()->where('name', 'like', "emit-print-order")->first();
                    if ($permissionService) {
                        Action::updateOrCreate(
                            [
                                'service_step_id' => $step->id,
                                'permission_service_id' => $permissionService->pivot->id,
                                'duration' => $step->duration,
                            ],
                            [
                                'process_type' => $step->process_type,
                                'author_id' => Profile::first()->id,
                                'position' => $i,
                                'pre_status' => Status::validated->name,
                                'post_status' => match ($service->type->code) {
                                    AvailableServiceType::IMMATRICULATION_STANDARD,
                                    AvailableServiceType::IMMATRICULATION_PRESTIGE_LABEL,
                                    AvailableServiceType::IMMATRICULATION_PRESTIGE_NUMBER,
                                    AvailableServiceType::IMMATRICULATION_PRESTIGE_NUMBER_LABEL,
                                    AvailableServiceType::GRAY_CARD_DUPLICATE,
                                    AvailableServiceType::PLATE_DUPLICATE,
                                    AvailableServiceType::PLATE_TRANSFORMATION,
                                    AvailableServiceType::VEHICLE_TRANSFORMATION => Status::print_order_emitted->name,
                                    default => Status::closed->name,
                                },
                                'description' => Permission::find($permissionService?->pivot->permission_id)?->label,
                            ]
                        );
                        $i++;
                    }
                    break;
                case Status::print_order_emitted->name:
                    $permissionService = $service->servicePermissions()->where('name', 'like', "print-%")->first();
                    if ($permissionService) {
                        Action::updateOrCreate(
                            [
                                'service_step_id' => $step->id,
                                'permission_service_id' => $permissionService->pivot->id,
                                'duration' => $step->duration,
                            ],
                            [
                                'process_type' => $step->process_type,
                                'author_id' => Profile::first()->id,
                                'position' => $i,
                                'pre_status' => Status::print_order_emitted->name,
                                'post_status' => Status::print_order_validated->name,
                                'description' => Permission::find($permissionService?->pivot->permission_id)?->label,
                            ]
                        );
                        $i++;
                    }
                    break;
                case Status::print_order_validated->name:
                    $permissionService = $service->servicePermissions()->where('name', 'like', "print-%")->first();
                    if ($permissionService) {
                        Action::updateOrCreate(
                            [
                                'service_step_id' => $step->id,
                                'permission_service_id' => $permissionService->pivot->id,
                                'duration' => $step->duration,
                            ],
                            [
                                'process_type' => $step->process_type,
                                'author_id' => Profile::first()->id,
                                'position' => $i,
                                'pre_status' => Status::print_order_validated->name,
                                'post_status' => Status::closed->name,
                                'description' => Permission::find($permissionService?->pivot->permission_id)?->label,
                            ]
                        );
                        $i++;
                    }
                    break;
                case Status::closed->name:
                    $permissionService = $service->servicePermissions()->where('name', 'like', "close-demand")->first();
                    if ($permissionService) {
                        Action::updateOrCreate(
                            [
                                'service_step_id' => $step->id,
                                'permission_service_id' => $permissionService->pivot->id,
                                'duration' => $step->duration,
                            ],
                            [
                                'process_type' => $step->process_type,
                                'author_id' => Profile::first()->id,
                                'position' => $i,
                                'pre_status' => Status::closed->name,
                                'post_status' => null,
                                'description' => Permission::find($permissionService?->pivot->permission_id)?->label,
                            ]
                        );
                        $i++;
                    }
                    break;
                default:
                    # code...
                    break;
            }
        }

        if (Step::where('status', Status::draft->name)->first()) {
            Step::whereIn('status', ['status' => Status::draft->name, Status::submitted->name])->delete();
        }
    }
}
