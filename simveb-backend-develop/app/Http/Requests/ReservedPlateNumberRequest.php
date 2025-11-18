<?php

namespace App\Http\Requests;

use App\Rules\NumberIsAlreadyReservedRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReservedPlateNumberRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'alphabetic_label' => ['required', 'alpha:ascii', 'max:6'],
            'numeric_label' => ['nullable', new NumberIsAlreadyReservedRule($this),'numeric', 'digits:4'],
            'min' => ['nullable',new NumberIsAlreadyReservedRule($this),'digits:4','numeric', Rule::requiredIf(isset($this->max))],
            'max' => ['nullable',new NumberIsAlreadyReservedRule($this),'digits:4','numeric', 'gt:min', Rule::requiredIf(isset($this->min))],
        ];
    }
}
