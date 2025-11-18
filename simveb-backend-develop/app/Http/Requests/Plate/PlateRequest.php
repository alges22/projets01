<?php

namespace App\Http\Requests\Plate;

use App\Imports\NonOrderedPlatesImport;
use App\Rules\ValidateExcelFileRule;
use Illuminate\Foundation\Http\FormRequest;

class PlateRequest extends FormRequest
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
        $import = new NonOrderedPlatesImport;

        return [
            'file' => ['bail', 'required', 'file', 'mimetypes:application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,text/csv', new ValidateExcelFileRule($import->rules(), $import->customValidationMessages(), $import->headingRow())],
        ];
    }

    public function messages()
    {
        return [
            'file.mimetypes' => 'Votre fichier doit être de type Excel(.xlsx) ou CSV',
        ];
    }
}
