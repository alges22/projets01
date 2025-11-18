<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ImmatriculationResource extends JsonResource
{
    public function toArray(Request $request)
    {
        return [
            'id' => $this->id,
            'demand_id' => $this->demand_id,
            'vehicle_id' => $this->vehicle_id,
            'vehicle_owner_id' => $this->vehicle_owner_id,
            'issued_at' => $this->issued_at,
            'immatriculation_format_id' => $this->immatriculation_format_id,
            'number_label' => $this->number_label,
            'plate_color_id' => $this->plate_color_id,
            'front_plate_shape_id' => $this->front_plate_shape_id,
            'back_plate_shape_id' => $this->back_plate_shape_id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'desired_number' => $this->desired_number,
            'label' => $this->label,
            'prefix' => $this->prefix,
            'alphabetic_label' => $this->alphabetic_label,
            'zone' => $this->zone,
            'numeric_label' => $this->numeric_label,
            'country_code' => $this->country_code,
            'formatLabel' => $this->formatLabel,
            'number' => $this->number,
            'number_template_id' => $this->number_template_id,

            'gray_card' => $this->grayCard,
            'active_gray_card' => $this->activeGrayCard,
            'front_plate_shape' => $this->frontPlateShape()->select(['id', 'name', 'code'])->first(),
            'back_plate_shape' => $this->backPlateShape()->select(['id', 'name', 'code'])->first(),
            'plate_color' => $this->plateColor()->select(['id', 'name', 'label', 'color_code', 'text_color'])->first(),
        ];
    }
}
