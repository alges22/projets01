<?php

namespace App\Http\Requests\Immatriculation;

use App\Enums\Status;
use App\Models\Order\Demand;
use App\Models\Treatment\Treatment;
use Illuminate\Foundation\Http\FormRequest;

class AssignToInterpolStaffRequest extends FormRequest
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
            'profile_id' => ['nullable','exists:profiles,id'],
            'demand_id' => ['required','exists:demands,id',
            function($attribute, $value, $fail){
                $demand = Demand::find($value);
                if (!in_array($demand?->status, [Status::affected_to_interpol->name])) {
                    return $fail('Le status de la demande est invalide.');
                }
            }],
        ];
    }
}
