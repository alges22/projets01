<?php
namespace Ntech\UserPackage\Http\Requests;

use App\Rules\Demands\ValideNpiRule;
use App\Rules\ValidateGuestNpiRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StaffRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() : bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules() : array
    {

        return  [
            "npi" => ['required', new ValideNpiRule, new ValidateGuestNpiRule],
            "roles" => ['array', 'required'],
            "roles.*" => ["exists:roles,name"],
            'position_id' => ['required','exists:positions,id'],
            "organizations" => ['array','required'],
            "organizations.*" => ['required','exists:organizations,id'],
            'center_id' => ['required','exists:management_centers,id']
        ];

    }
}
