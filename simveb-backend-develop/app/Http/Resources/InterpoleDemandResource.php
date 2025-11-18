<?php

namespace App\Http\Resources;

use App\Enums\Status;
use App\Models\Config\BlacklistVehicle;
use App\Models\Immatriculation\Immatriculation;
use App\Models\Mutation;
use App\Models\PlateTransformation;
use App\Models\Reimmatriculation;
use App\Models\SaleDeclaration;
use App\Traits\FormatFilesTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InterpoleDemandResource extends JsonResource
{
    use FormatFilesTrait;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $order =  $this->order();

        $model = $this->model->load($this->model::relations());

        if ($model instanceof SaleDeclaration) {
            $model->new_owner = $model->buyer;
        } elseif ($model instanceof Mutation) {
            $model->new_owner = $model->saleDeclaration->buyer;
        } elseif ($model instanceof Reimmatriculation && $model->immatriculation) {
            $model = $model->immatriculation->load(Immatriculation::relations());
        } elseif ($model instanceof Reimmatriculation && $model->plateTransformation) {
            $model = $model->vehicle->immatriculation->load(Immatriculation::relations());
        } elseif ($model instanceof PlateTransformation) {
            $model = $model->vehicle->immatriculation->load(Immatriculation::relations());
        }
        $vehicle = BlacklistVehicle::where('vin', $this->vehicle->vin)->where('status', Status::validated->name)->first();

        return [
            'demand' => [
                'reference' => $this->reference,
                'created_at' => $this->created_at,
                'submitted_at' => $this->submitted_at,
                'status' => $this->status,
                'status_label' => $this->statusLabel,
                'responsible_center' => $this->activeTreatment?->managementCenter?->name,
                'responsible' => $this->activeTreatment?->responsible?->identity?->fullName,
                'organization' => $this->activeTreatment?->organization?->name,
                'service' => $this->service->name,
                'service_code' => $this->service->type->code,
            ],
            'vehicle' => new ClientVehicleResource($this->vehicle),
            'vehicle_is_blacklited' => $vehicle ? true : false,
            'vehicle_owner' => new VehicleOwnerResource($this->vehicleOwner),
            'model' => $model,
            'files' => $this->formatFiles($this->files),
            'author' => [
                'name' => $this->author->identity->fullName,
                'email' => $this->author->identity->email,
                'npi' => $this->author->identity->npi,
                'profile_id' => $this->author->id,
                'telephone' => $this->author->identity->telephone,
                'company' => $this->author->space?->name
            ],
            'steps' => getDemandSteps($this->resource),
            'active_treatment' => $this->activeTreatment
        ];
    }
}
