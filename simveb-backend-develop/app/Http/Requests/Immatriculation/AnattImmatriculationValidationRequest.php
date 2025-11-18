<?php
namespace App\Http\Requests\Immatriculation;

use App\Enums\Status;
use App\Models\Order\Demand;
use Illuminate\Foundation\Http\FormRequest;

class AnattImmatriculationValidationRequest extends FormRequest
{
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
            'immatriculation_demand_id' => ['required', 'exists:demands,id', function ($attribute, $value, $fail) {
                $demand = Demand::findOrFail($value);

                if ($demand->status != Status::given_to_applicant->name) {
                    $fail("Impossible de faire cette action sur cette demande.");
                }
            }],
            'action' => ['required', 'in:validate,reject'],
            'observations' => ['nullable', 'string',],
        ];
    }
}
