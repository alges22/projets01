<?php

namespace App\Http\Requests\PoliceOfficerAssignment;

use App\Enums\ProfileTypesEnum;
use App\Enums\Status;
use App\Models\Auth\Profile;
use App\Models\PoliceOfficer\PoliceOfficerAssignment;
use Closure;
use Illuminate\Foundation\Http\FormRequest;

class PoliceOfficerAssignmentRequest extends FormRequest
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
        $this->merge(['author_id' => getOnlineProfile()->id]);

        return [
            'author_id' => ['required', 'uuid', 'exists:profiles,id'],
            'border_id' => ['required', 'uuid', 'exists:borders,id'],
            'profile_id' => [
                'bail',
                'required',
                'uuid',
                'exists:profiles,id',
                // TO-DO: Check if the profile is police type
                function (string $attribute, mixed $value, Closure $fail) {
                    if (Profile::find($value)->type->code !== ProfileTypesEnum::police->name) {
                        $fail("Le profile sélectionné n'est pas policier");
                    }
                },
                // TO-DO: Check if the police officer has already a pending assignment request
                function (string $attribute, mixed $value, Closure $fail) {
                    $assignment = PoliceOfficerAssignment::query()
                        ->where('profile_id', $this->profile_id)
                        ->where('status', Status::pending->name)
                        ->first();

                    if ($assignment != null) {
                        $fail("Une demande d'affectation est déjà en cours pour ce policier");
                    }
                }
            ],
        ];
    }
}
