<?php

namespace App\Http\Requests\Demand;

use App\Rules\Demands\ValidDemandOtpRule;
use App\Rules\Demands\ValideNpiRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class VerifyDemandOtpRequest extends FormRequest
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
        return [
            'authorization_id' => ['uuid','required','exists:demand_otps,id',],
            'owner_otp' => ['required','string','size:4', new ValidDemandOtpRule($this->authorization_id, 'owner_otp')],
            'buyer_otp' => ['sometimes','string','size:4', new ValidDemandOtpRule($this->authorization_id, 'buyer_otp')],
        ];
    }
}
