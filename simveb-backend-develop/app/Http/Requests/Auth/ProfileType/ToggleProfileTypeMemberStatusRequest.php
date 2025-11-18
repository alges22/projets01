<?php

namespace App\Http\Requests\Auth\ProfileType;

use App\Models\Auth\Profile;
use Illuminate\Foundation\Http\FormRequest;

class ToggleProfileTypeMemberStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'profile_id' => ['required', 'exists:profiles,id', function ($attribute, $value, $fail) {
                $onlineProfile = getOnlineProfile();
                $profile = $value ? Profile::find($value) : null;

                if ($profile && $profile->type_id != $onlineProfile->type_id) {
                    $fail(__("Cet utilisateur n'est pas un membre de cet espace."));
                }

                if ($profile && $value == $onlineProfile->id) {
                    $fail(__("Vous ne pouvez pas effectué cette action !"));
                }
            }]
        ];
    }
}
