<?php

namespace App\Http\Requests;

use App\Consts\Roles;
use App\Models\Pledge;
use App\Rules\ValidatePledgeRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use App\Enums\Status;

class ValidatePledgeRequest extends FormRequest
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
        $rules = [];
        $roles = getOnlineProfile()->roles->pluck('name')->toArray();
        $pledge = Pledge::query()->with('activeTreatment')->where('id', request()->validate)->first();

        if (in_array(Roles::BANK, $roles)) {
            $this->validateBank($pledge);
        }

        return $rules;
    }

    private function validateBank($pledge)
    {
        if ($pledge->status !== Status::emitted->name) {
            if (!isset($pledge->financial_institution) && !isset($pledge->activeTreatment->affected_to_institution)) {
                $this->fail("Vous n'êtes pas habilité à effectuer cette action");
            }else{
                $this->fail("Vous n'êtes pas habilité à effectuer cette action");
            }
        }
    }

    protected function fail($message)
    {
        abort(Response::HTTP_UNPROCESSABLE_ENTITY, $message);
    }
}
