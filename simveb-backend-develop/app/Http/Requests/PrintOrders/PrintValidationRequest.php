<?php

namespace App\Http\Requests\PrintOrders;

use App\Enums\PrintOrderTypesEnum;
use App\Enums\Status;
use App\Models\Treatment\PrintOrder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PrintValidationRequest extends FormRequest
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
            'print_order_id' => ['bail', 'required', 'exists:print_orders,id', function ($attribute, $value, $fail) {
                // $printOrder = PrintOrder::find($value);

                // if (
                //     $printOrder->type == PrintOrderTypesEnum::gray_card->name ||
                //     in_array($printOrder->type, [PrintOrderTypesEnum::both->name, PrintOrderTypesEnum::plate->name]) && $printOrder->status != Status::plate_printed->name
                // ) {
                //     $fail('Impossible de faire cette action sur cet ordre d\'impression.');
                // }
            }],
            'action' => ['bail', 'required', 'in:validate,reject'],
            'observations' => ['nullable', Rule::requiredIf($this->action == 'reject'), 'string', 'max:255'],
            'images' => ['nullable', Rule::requiredIf($this->action == 'validate'), 'array',],
            'images.*' => ['bail', 'required', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:10240']
        ];
    }
}
