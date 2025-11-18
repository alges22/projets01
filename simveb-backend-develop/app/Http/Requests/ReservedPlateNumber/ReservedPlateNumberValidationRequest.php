<?php

namespace App\Http\Requests\ReservedPlateNumber;

use App\Enums\Status;
use App\Models\Config\ReservedPlateNumber;
use App\Rules\NumberIsAlreadyReservedRule;
use Illuminate\Foundation\Http\FormRequest;

class ReservedPlateNumberValidationRequest extends FormRequest
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
            'reserved_plate_number_id' => ['required', 'exists:reserved_plate_numbers,id', new NumberIsAlreadyReservedRule($this)],
            'action' => ['required', 'in:validate,reject'],
        ];
    }
}
