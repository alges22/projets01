<?php

namespace App\Http\Requests\Immatriculation;

use App\Enums\Status;
use App\Models\Order\Demand;
use App\Rules\IsInterpolServiceRule;
use Illuminate\Foundation\Http\FormRequest;

class AssignToInterpolRequest extends FormRequest
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
            'demand_id' => ['required','exists:demands,id','uuid',
            function($attribute, $value, $fail){
                $demand = Demand::find($value);
                if (!in_array($demand?->status, [Status::verified->name])) {
                    return $fail('Le status de la demande est invalide.');
                }
            }],
        ];
    }
}
