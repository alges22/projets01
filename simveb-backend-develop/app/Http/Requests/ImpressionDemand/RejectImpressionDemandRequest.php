<?php

namespace App\Http\Requests\ImpressionDemand;

use Illuminate\Foundation\Http\FormRequest;

class RejectImpressionDemandRequest extends FormRequest
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
            'impression_demand_id' => ['required', 'exists:impression_demands,id'],
            'reason' => ['required', 'string'],
        ];
    }
}
