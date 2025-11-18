<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ActionRequest extends FormRequest
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
        $id = $this->route('id');

        return [
            'service_step_id' => ['required', 'exists:service_steps,id'],
            'permission_service_id' => ['required', 'exists:permission_services,id'],
            'position' => [
                'required',
                'integer',
                'min:1', 
                Rule::unique('actions')->ignore($id)->where(function ($query) {
                    return $query->where('service_step_id', $this->service_step_id);
                })
            ],
            'duration' => ['required', 'integer', 'min:1'],
            'process_type' => ['required', 'string', 'in:automatic,manual'],
            'author_id' => ['required'],
        ];
    }
}
