<?php

namespace App\Http\Requests\Space;

use App\Enums\Status;
use Illuminate\Foundation\Http\FormRequest;

class ValidateOrRejectSpaceSuspensionRequest extends FormRequest
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
        $suspension = $this->route('space_suspension_request');

        return [
            'action' => ['required', 'string', 'in:validate,reject', function ($attribute, $value, $fail) use ($suspension) {
                if ($suspension && $suspension->status != Status::pending->name) {
                    $fail('Vous ne pouvez pas faire cette action sur cette demande de suspension');
                }
            }],
            'reject_reason' => ['required_if:action,reject', 'string',],
        ];
    }
}
