<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ClientVehicleCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->collection?->transform(function ($clientVehicle) {
            return [
                'id' => $clientVehicle->id,
                'vin' => $clientVehicle->vin,
                'immatriculation' => $clientVehicle->immatriculation?->number_label,
            ];
        })->toArray();
    }
}
