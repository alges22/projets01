<?php

namespace App\Http\Requests;

use App\Enums\Status;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class PrestigeLabelImmatriculationRequest extends FormRequest
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
            'desired_label' => ['required','string',
            function ($attribute, $value, $fail) {
                $exists = DB::table('immatriculation_labels')
                    ->where('label', $value)
                    ->doesntExist();

                if ($exists) {
                    $fail("Désolé l\'immatriculation de prestige $attribute existe déjà.");
                }
            },],
            'immatriculation_id' => ['required','exists:immatriculations,id'],
            'comment' => ['nullable', 'string'],
            'documents' => ['nullable', 'array'],
            'service_id' => ['required','exists:services,id'],
            'payment_status' => ['required',Rule::in(Status::toArray())]
        ];
    }
}
