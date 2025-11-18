<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NumberTemplateRequest extends FormRequest
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
        $this->merge(['author_id' => getOnlineProfile()->id]);

        return [
            'template' => ['required','string','size:4',Rule::unique('number_templates','template')->ignore($this->number_template?->id)],
            'author_id' => ['required']
        ];
    }
}
