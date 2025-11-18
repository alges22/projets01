<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ImmatriculationLabelResource extends JsonResource
{
    public function toArray(Request $request)
    {
        $immatriculation = $this->immatriculation;

        return [
            'id' => $this->id,
            'demand_id' => $this->demand_id,
            'vehicle_id' => $this->vehicle_id,
            'vehicle_owner_id' => $this->vehicle_owner_id,
            'issued_at' => $this->issued_at,
            'immatriculation_format_id' => $immatriculation->immatriculation_format_id,
            'number_label' => $immatriculation->number_label,
            'plate_color_id' => $this->plate_color_id,
            'front_plate_shape_id' => $this->front_plate_shape_id,
            'back_plate_shape_id' => $this->back_plate_shape_id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'desired_number' => $immatriculation->desired_number,
            'label' => $immatriculation->label,
            'prefix' => $immatriculation->prefix,
            'alphabetic_label' => $immatriculation->alphabetic_label,
            'zone' => $immatriculation->zone,
            'numeric_label' => $immatriculation->numeric_label,
            'country_code' => $immatriculation->country_code,
            'formatLabel' => $immatriculation->formatLabel,
            'number' => $immatriculation->number,
            'number_template_id' => $immatriculation->number_template_id,

            'gray_card' => $immatriculation->grayCard,
            'active_gray_card' => $immatriculation->activeGrayCard,
            'front_plate_shape' => $this->frontPlateShape()->select(['id', 'name', 'code'])->first(),
            'back_plate_shape' => $this->backPlateShape()->select(['id', 'name', 'code'])->first(),
            'plate_color' => $this->plateColor()->select(['id', 'name', 'label', 'color_code', 'text_color'])->first(),
        ];
    }
}
