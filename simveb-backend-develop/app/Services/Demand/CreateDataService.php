<?php

namespace App\Services\Demand;

use App\Consts\AvailableServiceType;
use App\Http\Resources\ClientDemandResource;
use App\Http\Resources\ClientVehicleResource;
use App\Models\Config\NumberTemplate;
use App\Models\Config\ReimmatriculationReason;
use App\Models\Config\Service;
use App\Models\Config\TitleReason;
use App\Models\Config\TransformationType;
use App\Models\Order\Demand;
use App\Models\Plate\PlateColor;
use App\Models\Plate\PlateShape;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class CreateDataService
{

    public function getCreateData(Service $service): array
    {
        $data = [];

        switch ($service->type->code) {
            case AvailableServiceType::IMMATRICULATION_STANDARD:
            case AvailableServiceType::IMMATRICULATION_PRESTIGE_NUMBER_LABEL:
            case AvailableServiceType::IMMATRICULATION_PRESTIGE_NUMBER:
                $data = [
                    'required_documents' => $service->documents()->select(['id', 'description'])->get(),
                    'plate_colors' => PlateColor::query()->select(['label', 'id', 'color_code'])->get(),
                    'plate_shapes' => PlateShape::query()->select(['name', 'id'])->get(),
                    'number_templates' => NumberTemplate::query()->select(['template', 'id'])->get(),
                ];
                break;
            case AvailableServiceType::MUTATION:
            case AvailableServiceType::TINTED_WINDOW_AUTHORIZATION:
            case AvailableServiceType::GLASS_ENGRAVING:
            case AvailableServiceType::SALE_DECLARATION:
                $data = [
                    'required_documents' => $service->documents()->select(['id', 'description'])->get(),
                ];
                break;
            case AvailableServiceType::PLATE_TRANSFORMATION:
            case AvailableServiceType::IMMATRICULATION_PRESTIGE_LABEL:
                $data = [
                    'required_documents' => $service->documents()->select(['id', 'description'])->get(),
                    'plate_colors' => PlateColor::query()->select(['label', 'id', 'color_code'])->get(),
                    'plate_shapes' => PlateShape::query()->select(['name', 'id'])->get(),
                ];
                break;
            case AvailableServiceType::TITLE_RECOVERY:
            case AvailableServiceType::TITLE_DEPOSIT:
                $data = [
                    'required_documents' => $service->documents()->select(['id', 'description'])->get(),
                    'title_reasons' => TitleReason::query()->select(['id', 'label'])->get(),
                ];
                break;
            case AvailableServiceType::RE_IMMATRICULATION:
                $data = [
                    'required_documents' => $service->documents()->select(['id', 'description'])->get(),
                    'reasons' => ReimmatriculationReason::select(['id', 'code', 'title', 'requires_reason', 'requires_title_deposit', 'requires_transfer_certificate', 'enable_plate_transformation'])->get(),
                    'plate_colors' => PlateColor::query()->select(['label', 'id', 'color_code'])->get(),
                    'plate_shapes' => PlateShape::query()->select(['name', 'id'])->get(),
                    'number_templates' => NumberTemplate::query()->select(['template', 'id'])->get(),
                    'services' => Service::query()
                        ->whereHas('type', function ($query) {
                            $query->where('code', AvailableServiceType::IMMATRICULATION);
                        })
                        ->first()?->children,
                ];
                break;
            case AvailableServiceType::VEHICLE_TRANSFORMATION:
                $data = [
                    'required_documents' => $service->documents()->select(['id', 'description'])->get(),
                    'transformationTypes' => TransformationType::select('id', 'label', 'description')
                        ->with(['categoryCharacteristics' => function ($query) {
                            $query->select('id', 'label', 'code')
                            ->with(['vehicleCharacteristics' => function ($query) {
                                $query->select('id', 'category_id', 'value', 'code');
                            }]);
                        }])
                        ->get(),
                ];
                break;
            default:
                abort(ResponseAlias::HTTP_BAD_REQUEST, "Type de service inconnu");
        }

        if (request()->filled('demand')) {
            $data['demand'] = new ClientDemandResource(Demand::query()->find(request()->input('demand')));
        }

        return $data;
    }

    public function getEditData(Demand $demand)
    {
        return [
            'demand' => $demand->load($demand::relations()),
            'service' => $this->getCreateData($demand->service),
            'vehicle' => $demand->vehicle ? new ClientVehicleResource($demand->vehicle) : null,
        ];
    }
}
