<?php

namespace App\Http\Requests;

use App\Imports\GmaVehicleImport;
use App\Rules\ValidateExcelFileRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ImportGmaRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        $import = new GmaVehicleImport(null, null);

        return [
            'file' => ['bail', 'required', 'file', 'mimetypes:application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,text/csv', new ValidateExcelFileRule($import->rules(), $import->customValidationMessages(), $import->headingRow())],
            'declaration_file' => ['required', 'file'],
        ];
    }

    public function messages()
    {
        return [
            'file.mimetypes' => 'Votre fichier doit être de type Excel(.xlsx) ou CSV',
        ];
    }
}
