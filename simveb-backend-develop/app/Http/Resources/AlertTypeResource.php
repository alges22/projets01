<?php

namespace App\Http\Resources;

use App\Http\Resources\Vehicle\VehicleAlertResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AlertTypeResource extends JsonResource
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
            'name' => $this->name,
            'code' => $this->code,
            'image' => asset(json_decode($this->image)->path),
            'vehicle_alerts' => VehicleAlertResource::collection($this->whenLoaded('vehicleAlerts')),
        ];
    }
}
