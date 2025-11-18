<?php

namespace App\Http\Requests\Immatriculation;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class ImmatriculationNumberRequest extends FormRequest
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
            'immatriculation_number' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (DB::table('immatriculations')->whereRaw('TRIM(number_label) = ?', [$value])->doesntExist())
                    {
                        $fail("Le numéro d'immatriculation est introuvable.");
                    }
                },
            ],
        ];
    }
}
