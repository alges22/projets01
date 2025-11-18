<?php

namespace App\Http\Resources\Vehicle;

use App\Enums\Status;
use App\Http\Resources\ClientVehicleResource;
use App\Http\Resources\ProfileResource;
use App\Http\Resources\VehicleOwnerResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleAlertResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'id' => $this->id,
            'status' => $this->status,
            'driver_firstname' => $this->driver_firstname,
            'driver_lastname' => $this->driver_lastname,
            'created_at' => $this->created_at,
            'vehicle_owner' => new VehicleOwnerResource($this->vehicle->owner),
            'vehicle' => new ClientVehicleResource($this->whenLoaded('vehicle')),
            'officer' => new ProfileResource($this->whenLoaded('officer')),
            'alert_types' => $this->whenLoaded('alertTypes'),
        ];
    }
}
