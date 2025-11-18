<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ZoneRequest extends FormRequest
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
            'code' => ['string','required',Rule::unique('zones','code')->ignore($this->route()->parameter('zone'))],
            'name' => ['string','required',Rule::unique('zones','name')->ignore($this->route()->parameter('zone'))],
            'towns' => ['required', 'array'],
            'towns.*' => ['exists:towns,id',],
        ];
    }
}
