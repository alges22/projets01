<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnnatServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required'],
            'description' => ['required'],
            'type' => ['required'],
            'duration' => ['required'],
            'cost' => ['required'],
            'procedures' => ['required'],
            'who_can_apply' => ['required'],
            'link' => ['nullable'],
            'administration_id' => ['nullable'],
            'documents_required' => ['required'],
        ];
    }
}
