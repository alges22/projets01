<?php

namespace App\Http\Requests;

use App\Enums\Status;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VehicleAdministrativeStatusRequest extends FormRequest
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
            'situation_type' => ['required', 'string', 'max:225'],
            'motif' => ['nullable', 'string', 'max:225'],
            'vehicle_id' => ['required', 'exists:vehicles,id', Rule::unique('vehicle_administrative_statuses', 'vehicle_id')->ignore($this->vehicleAdministrativeStatus?->id)],
            'declarant_id' => ['required', 'exists:declarants,id'],
            'service_id' => ['required', 'exists:services,id'],
            'user_id' => ['required'],
            'payment_status' => ['required',Rule::in(Status::toArray())],
        ];
    }
}
