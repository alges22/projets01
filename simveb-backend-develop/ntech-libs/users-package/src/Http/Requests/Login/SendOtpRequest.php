<?php
namespace Ntech\UserPackage\Http\Requests\Login;

use Illuminate\Foundation\Http\FormRequest;

class SendOtpRequest extends FormRequest
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
            'npi' => ['required', 'exists:users,username'],
        ];
    }

    public function messages()
    {
        return [
            'npi.exists' => "NPI incorrect ou vous n'avez pas encore de compte.",
        ];
    }
}
