<?php

namespace App\Http\Requests\Duplicate;

use App\Enums\Status;
use App\Rules\Demands\ValidVINRule;
use App\Rules\Demands\VehicleIsImmatriculatedRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GrayCardDuplicateRequest extends FormRequest
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
    public static function storeRules(FormRequest $request): array
    {
        return [
            'comment' => ['nullable', 'string'],
            'is_spoiled' => ['nullable', 'boolean', Rule::requiredIf(!isset($request->is_lost))],
            'is_lost' => ['nullable', 'boolean',Rule::requiredIf(!isset($request->is_spoiled))],
            'documents' => ['nullable', 'array'],
            'vin' => ['required', new ValidVINRule, new VehicleIsImmatriculatedRule],
            'service_id' => ['required','exists:services,id'],
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public static function addToCartRules(FormRequest $request): array
    {

        return [
            'comment' => ['nullable', 'string'],
            'is_spoiled' => ['nullable', 'boolean', Rule::requiredIf(!isset($request->is_lost))],
            'is_lost' => ['nullable', 'boolean',Rule::requiredIf(!isset($request->is_spoiled))],
            'documents' => ['nullable', 'array'],
            'vin' => ['required', new ValidVINRule, new VehicleIsImmatriculatedRule],
            'service_id' => ['required','exists:services,id'],
        ];
    }
}
