<?php

namespace App\Http\Requests\Plate;

use App\Imports\InstitutionPlatesImport;
use App\Models\Plate\PlateOrder;
use App\Rules\CanConfirmPlateOrderRule;
use App\Rules\ValidateExcelFileRule;
use Illuminate\Foundation\Http\FormRequest;

class ConfirmPlateOrderRequest extends FormRequest
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
        $plateOrder = PlateOrder::find($this->plate_order_id);

        $import = !$plateOrder ? null : new InstitutionPlatesImport();
        $fileRules = !$import ? [] : $import->rules();
        $fileRuleMessages = !$import ? [] : $import->customValidationMessages();

        return [
            'plate_order_id' => ['bail', 'required', 'exists:plate_orders,id', new CanConfirmPlateOrderRule($plateOrder)],
            'file' => ['bail', 'required', 'file', 'mimetypes:application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,text/csv', new ValidateExcelFileRule($fileRules, $fileRuleMessages, $import->headingRow())],
        ];
    }

    public function messages()
    {
        return [
            'file.mimetypes' => 'Votre fichier doit être de type Excel(.xlsx) ou CSV',
        ];
    }
}
