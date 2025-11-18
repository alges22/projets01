<?php

namespace App\Http\Requests\Immatriculation;

use App\Enums\Status;
use App\Models\Order\Demand;
use Illuminate\Foundation\Http\FormRequest;

class AssignToServiceRequest extends FormRequest
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
            'organization_id' => ['nullable','exists:organizations,id'],
            'demand_id' => ['required','exists:demands,id',
                function($attribute, $value, $fail){
                    $demand = Demand::find($value);
                    if (in_array($demand?->status, [Status::closed->name, Status::pending->name, Status::submitted->name])) {
                        return $fail('Le status de la demande est invalide.');
                    }
                }],
        ];
    }
}
