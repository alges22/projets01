<?php

namespace App\Http\Requests\Auth\Profile;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
            'town_id' => ['required','uuid','exists:towns,id'],
            'district_id' => ['required','uuid','exists:districts,id'],
            'state_id' => ['required','uuid','exists:states,id'],
            'village_id' => ['required','uuid','exists:villages,id'],
        ];
    }
}
