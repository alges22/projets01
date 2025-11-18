<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VillageRequest extends FormRequest
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
            'code' => ['string','required',Rule::unique('villages','code')->ignore($this->route()->parameter('village'))],
            'name' => ['string','required',Rule::unique('villages','name')->ignore($this->route()->parameter('village'))],
            'district_id' => ['required','uuid','exists:districts,id']
        ];
    }
}
