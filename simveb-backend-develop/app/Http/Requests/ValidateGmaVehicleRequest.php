<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class ValidateGmaVehicleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return getOnlineProfile()->hasPermissionTo('validate-' . Str::kebab('GmaVehicle'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'gma_vehicles' => ['required','array'],
            'gma_vehicles.*' => ['exists:gma_vehicles,id']
        ];
    }
}
