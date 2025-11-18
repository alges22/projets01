<?php

namespace App\Http\Requests;

use App\Http\Requests\Demand\GetDemandRules;
use App\Models\Config\Service;
use Illuminate\Foundation\Http\FormRequest;

class AddCharacteristicRequest extends FormRequest
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
        $rules = GetDemandRules::addCharacteristicsRule($this);

        return [
            ...$rules
        ];
    }
}
