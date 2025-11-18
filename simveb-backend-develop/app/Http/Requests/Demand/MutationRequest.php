<?php

namespace App\Http\Requests\Demand;

use App\Models\SaleDeclaration;
use App\Rules\Demands\ValidVINRule;
use App\Rules\Demands\VehicleHasGrayCardRule;
use App\Rules\Demands\VehicleIsImmatriculatedRule;
use Illuminate\Foundation\Http\FormRequest;
use Closure;

class MutationRequest extends FormRequest
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
    public static function storeRules(FormRequest $request): array
    {
        $saleDeclaration = SaleDeclaration::where('reference', $request->sale_declaration_reference)->first();
        return [
            'vin' => ['required', new ValidVINRule, new VehicleIsImmatriculatedRule, new VehicleHasGrayCardRule],
            'sale_declaration_reference' => ['required', 'exists:sale_declarations,reference'],
            'comment' => ['nullable', 'string'],
            'npi' => ['string', function (string $attribute, mixed $value, Closure $fail) use ($saleDeclaration) {
                if (getOnlineProfile()->isUserProfile()) {
                    if (getOnlineProfile()->identity->npi != $saleDeclaration?->new_owner_npi) {
                        $fail('Désolé vous n\'êtes pas l\'acheteur de ce véhicule.');
                    }
                } elseif ($value != $saleDeclaration?->new_owner_npi) {
                    $fail('Désolé vous n\'êtes pas l\'acheteur de ce véhicule.');
                }
            },],
            'ifu' => [function ($attribute, $value, $fail) use ($saleDeclaration) {
                if ($value && $value != $saleDeclaration?->new_owner_ifu) {
                    $fail('Désolé vous n\'êtes pas l\'acheteur de ce véhicule.');
                }
            },],
        ];
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public static function addToCartRules(FormRequest $request): array
    {

        $saleDeclaration = SaleDeclaration::where('reference', $request->sale_declaration_reference)->first();
        return [
            'vin' => ['required', new ValidVINRule, new VehicleIsImmatriculatedRule, new VehicleHasGrayCardRule],
            'sale_declaration_reference' => ['required', 'exists:sale_declarations,reference'],
            'comment' => ['nullable', 'string'],
            'npi' => ['string', function (string $attribute, mixed $value, Closure $fail) use ($saleDeclaration) {
                if (getOnlineProfile()->isUserProfile()) {
                    if (getOnlineProfile()->identity->npi != $saleDeclaration?->new_owner_npi) {
                        $fail('Désolé vous n\'êtes pas l\'acheteur de ce véhicule.');
                    }
                } elseif ($value != $saleDeclaration?->new_owner_npi) {
                    $fail('Désolé vous n\'êtes pas l\'acheteur de ce véhicule.');
                }
            },],
            'ifu' => [function ($attribute, $value, $fail) use ($saleDeclaration) {
                if (isset($value) && $value != $saleDeclaration?->new_owner_ifu) {
                    $fail('Désolé vous n\'êtes pas l\'acheteur de ce véhicule.');
                }
            },],
        ];
    }
}
