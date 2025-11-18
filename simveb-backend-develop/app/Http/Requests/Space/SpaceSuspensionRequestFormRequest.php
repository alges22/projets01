<?php

namespace App\Http\Requests\Space;

use App\Enums\Status;
use App\Models\Space\Space;
use Illuminate\Foundation\Http\FormRequest;

class SpaceSuspensionRequestFormRequest extends FormRequest
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
            'space_id' => ['required', 'exists:spaces,id', function ($attribute, $value, $fail) use ($suspension) {
                if ($value) {
                    if ($suspension && $suspension->status != Status::pending->name) {
                        $fail('Vous ne pouvez pas modifier cette demande de suspension');
                    }

                    $space = Space::find($value);
                    if ($space && $space->status == Status::suspended->name) {
                        $fail('Cet espace est déjà suspendu.');
                    }
                }
            }],
        ];
    }
}
