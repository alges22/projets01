<?php

namespace Ntech\UserPackage\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Ntech\UserPackage\Rules\UpdateStaffStatusRule;

class StaffStatusRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'status' => ['required', 'in:online,offline', new UpdateStaffStatusRule($this->staff)],
        ];
    }
}
