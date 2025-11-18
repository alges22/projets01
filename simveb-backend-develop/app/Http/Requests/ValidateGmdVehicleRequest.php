<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ValidateGmdVehicleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return getOnlineProfile()->hasPermissionTo('validate-' . Str::kebab('GmdVehicle'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $this->merge(['validator_id' => getOnlineProfile()->id]);
        return [
            'gmd_vehicles' => ['required', 'array'],
            'gmd_vehicles.*' => ['required', 'uuid', Rule::exists('gmd_vehicles', 'id')],
            'validator_id' => ['required', 'uuid', Rule::exists('profiles', 'id')],
        ];
    }
}
