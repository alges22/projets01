<?php

namespace App\Http\Requests\Demand;

use Illuminate\Foundation\Http\FormRequest;

class ValidateOrRejectDemandUpdatesRequest extends FormRequest
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
            'demand_updates' => ['required', 'array'],
            'demand_updates.*' => ['required', 'uuid', 'exists:demand_updates_histories,id']
        ];
    }
}
