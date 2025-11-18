<?php

namespace App\Http\Resources\Vehicle;

use App\Enums\VehiclePassageType;
use App\Enums\VehicleTypeAtBorder;
use App\Http\Resources\ClientVehicleResource;
use App\Http\Resources\ProfileResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehiclePassageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {


        $data = [
            'id' => $this->id,
            'vehicle_provenance' => $this->vehicle_provenance_label,
            'immatriculation_number' => $this->immatriculation_number,
            'vehicle_owner_firstname' => $this->vehicle_owner_firstname,
            'vehicle_owner_lastname' => $this->vehicle_owner_lastname,
            'driver_firstname' => $this->driver_firstname,
            'driver_lastname' => $this->driver_lastname,
            'driver_telephone' => $this->driver_telephone,
            'passage_type' => $this->passage_type_label,
            'created_at' => $this->created_at,
            'date' => $this->created_date,
            'officer' => new ProfileResource($this->whenLoaded('officer')),
            'driving_license_photo' => $this->driving_license_photo,
            'gray_card_photo' => $this->gray_card_photo,
        ];

        $this->vehicle ?? null
            ? $data['vehicle'] = new ClientVehicleResource($this->whenLoaded('vehicle'))
            : $data['vehicle'] = null;


        return $data;
    }
}
