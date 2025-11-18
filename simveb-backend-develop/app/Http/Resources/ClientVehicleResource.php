<?php

namespace App\Http\Resources;

use App\Models\Vehicle\VehicleCharacteristicCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientVehicleResource extends JsonResource
{

    public function __construct($resource, private readonly string $source = 'database')
    {
        parent::__construct($resource);
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $characteristics = [];

        if ($this->source == 'database') {
            foreach (VehicleCharacteristicCategory::whereNotNull('field_name')->get() as $category) {
                $characteristics[] = [$category->label => $this->resource->characteristics()->where('category_id', $category->id)->first()?->value ?? null];
            }

            $data = [
                "id" => $this->id,
                "origin_country" => $this->originCountry?->name,
                "vin" => $this->vin,
                "immatriculation_number" => $this->immatriculation?->number,
                "immatriculation" => $this->immatriculation?->number_label,
                "vehicle_brand" => $this->brand?->name,
                "vehicle_model" => $this->vehicle_model,
                "number_of_seats" => $this->number_of_seats,
                "engin_number" => $this->engin_number,
                "charged_weight" => $this->charged_weight,
                "empty_weight" => $this->empty_weight,
                "first_circulation_year" => $this->first_circulation_year,
                "park" => $this->park?->name,
                'characteristics' => $characteristics,
                'nb_plate' => $this->category?->nb_plate,
                'is_car' => $this->category?->nb_plate == 2 ? true : false,
                'title_deposits' => $this->resource->titleDeposits?->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'date' => $item->created_at,
                        'reason' => $item->reason->label
                    ];
                })
            ];
        } else {
            foreach (VehicleCharacteristicCategory::whereNotNull('field_name')->get() as $category) {
                $characteristics[] = [$category->label => $this->resource[$category->field_name] ?? null];
            }

            $data = [
                'id' => $this['id'] ?? null,
                'origin_country' => $this['origin_country'],
                'vin' => $this['vin'],
                'vehicle_brand' => $this['vehicle_brand'],
                'vehicle_model' => $this['vehicle_model'],
                'number_of_seats' => $this['number_of_seats'],
                'vehicle_energy' => $this['vehicle_energy'],
                'engin_number' => $this['engin_number'],
                'charged_weight' => $this['charged_weight'],
                'empty_weight' => $this['empty_weight'],
                'first_circulation_year' => $this['first_circulation_year'],
                'park' => $this['park'],
                'characteristics' => $characteristics,
                'is_car' => $this['is_car'],
                'title_deposits' => []
            ];
        }

        return $data;
    }
}
