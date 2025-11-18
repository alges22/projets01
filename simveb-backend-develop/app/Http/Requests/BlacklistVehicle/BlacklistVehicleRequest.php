<?php

namespace App\Http\Requests\BlacklistVehicle;

use App\Enums\VehicleTypeAtBorder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BlacklistVehicleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'vin' => ['string', 'required', Rule::unique('blacklist_vehicles','vin')->ignore($this->id)],
        ];
    }
}
