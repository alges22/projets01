<?php

namespace App\Http\Requests\Space;

use App\Enums\Status;
use App\Models\Space\Space;
use Illuminate\Foundation\Http\FormRequest;

class SpaceSuspensionRequesLiftingtFormRequest extends FormRequest
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
        $lifting = $this->route('space_suspension_lifting_request');

        return [
            'space_id' => ['required', 'exists:spaces,id', function ($attribute, $value, $fail) use ($lifting) {
                if ($value) {
                    if ($lifting && $lifting->status != Status::pending->name) {
                        $fail('Vous ne pouvez pas modifier cette demande de levée de suspension');
                    }

                    $space = Space::find($value);
                    if ($space && $space->status == Status::active->name) {
                        $fail("Cet espace n'est pas suspendu.");
                    }
                }
            }],
        ];
    }
}
