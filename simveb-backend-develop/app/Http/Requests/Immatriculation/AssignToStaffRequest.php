<?php

namespace App\Http\Requests\Immatriculation;

use App\Enums\Status;
use App\Models\Order\Demand;
use App\Rules\Demands\StaffCanHandleDemandRule;
use App\Rules\Demands\ValideNpiRule;
use Illuminate\Foundation\Http\FormRequest;

class AssignToStaffRequest extends FormRequest
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
            //TODO check if demand is already assigned to an organization
            'npi' => ['nullable', 'numeric', 'digits:10', 'exists:identities,npi', new ValideNpiRule, new StaffCanHandleDemandRule],
            'demand_id' => ['required', 'exists:demands,id',
            function($attribute, $value, $fail){
                $demand = Demand::find($value);
                if (!in_array($demand?->status, [Status::assigned_to_service->name, Status::assigned_to_staff->name])) {
                    return $fail('Le status de la demande est invalide.');
                }
            }],
        ];
    }
}
