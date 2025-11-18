<?php

namespace App\Http\Requests\Vehicle;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VehicleOwnerRequest extends FormRequest
{
    private $ownerId;

    public function __construct($ownerId = null)
    {
        $this->ownerId = $ownerId;
    }

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
        if ($this->ownerId) {
            $id = $this->ownerId;
        } else {
            $id = $this->filled('id') ? $this->id : $this->vehicle_owner?->id;
        }

        $rules = [
            'ifu' => [
                'nullable','digits:13',Rule::unique('vehicle_owners','ifu')
                ->ignore($id)
            ],
            'npi' =>[
                'required','numeric',Rule::unique('vehicle_owners','npi')
                    ->ignore($id)
            ] ,
            'bfu' => [
                'nullable',Rule::unique('vehicle_owners','bfu')
                    ->ignore($id)
            ],
            'nationality_id' => ['nullable', 'exists:countries,id'],
            'birthdate' => ['nullable','sometimes','date','date_format:Y-m-d'],
            'birth_place' => ['string','nullable',],
            'town_id' => ['nullable','exists:towns,id'],
            'house' => ['string','nullable'],
            'district' => ['string','nullable'],
            'address' => ['string','nullable'],
            'telephone' => ['nullable'],
            'email' => ['nullable','email',Rule::unique('vehicle_owners','email')
                ->ignore($id)],
            'sex' => ['sometimes',Rule::in(['F','M'])],
            'owner_type_id' => ['nullable','exists:owner_types,id'],
            'legal_status_id' =>  ['nullable','exists:legal_statuses,id'],
        ];

        if ($this->ownerId) {
            $rules = array_merge(
                $rules,
                [
                    'firstname' => ['string', 'nullable'],
                    'lastname' => ['string','nullable'],
                    'name' => ['nullable','string',],
                ]);
        } else {
            $rules = array_merge(
                $rules,
                [
                    'firstname' => ['string',Rule::requiredIf($this->isNotFilled('name'))],
                    'lastname' => ['string',Rule::requiredIf($this->isNotFilled('name'))],
                    'name' => ['nullable','string',Rule::requiredIf($this->isNotFilled('lastname'))],
                ]);
        }

        return $rules;
    }

}
