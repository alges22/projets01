<?php

namespace App\Http\Requests\Demand;

use App\Http\Requests\Demand\SaleDeclarationRequest;
use App\Consts\AvailableServiceType;
use App\Http\Requests\Demand\VehicleTransformationRequest;
use App\Http\Requests\Duplicate\GrayCardDuplicateRequest;
use App\Http\Requests\GlassEngravingRequest;
use App\Models\Config\Service;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Http\Requests\Duplicate\PlateDuplicateRequest;

class GetDemandRules
{

    public static function getDemandRuleByService(?Service $service, FormRequest $request, bool $addingToCart = false): array
    {
        $rules =  match ($service?->type?->code){
            AvailableServiceType::IMMATRICULATION_STANDARD,
            AvailableServiceType::IMMATRICULATION_PRESTIGE_NUMBER,
            AvailableServiceType::IMMATRICULATION_PRESTIGE_NUMBER_LABEL => !$addingToCart ? ImmatriculationRequest::storeRules($request) : ImmatriculationRequest::addToCartRules($service, $request),
            AvailableServiceType::IMMATRICULATION_PRESTIGE_LABEL => !$addingToCart ? ImmatriculationLabelRequest::storeRules($service, $request) : ImmatriculationLabelRequest::addToCartRules($service, $request),
            AvailableServiceType::SALE_DECLARATION => !$addingToCart ? SaleDeclarationRequest::storeRules($request) : SaleDeclarationRequest::addToCartRules($request),
            AvailableServiceType::MUTATION => !$addingToCart ? MutationRequest::storeRules($request) : MutationRequest::addToCartRules($request),
            AvailableServiceType::TITLE_DEPOSIT => !$addingToCart ? TitleDepositRequest::storeRules($service, $request) : TitleDepositRequest::addToCartRules($service, $request),
            AvailableServiceType::TITLE_RECOVERY => !$addingToCart ? TitleRecoveryRequest::storeRules($service, $request) : TitleRecoveryRequest::addToCartRules($service, $request),
            AvailableServiceType::RE_IMMATRICULATION => !$addingToCart ? ReimmatriculationRequest::storeRules($request) : ReimmatriculationRequest::addToCartRules($request),
            AvailableServiceType::PLATE_TRANSFORMATION => !$addingToCart ? PlateTransformationRequest::storeRules($service, $request) : PlateTransformationRequest::addToCartRules($service, $request),
            AvailableServiceType::TINTED_WINDOW_AUTHORIZATION => !$addingToCart ? TintedWindowAuthorizationRequest::storeRules($service, $request) : TintedWindowAuthorizationRequest::addToCartRules($service, $request),
            AvailableServiceType::PLATE_DUPLICATE => !$addingToCart ? PlateDuplicateRequest::storeRules($request) : PlateDuplicateRequest::addToCartRules($request),
            AvailableServiceType::GRAY_CARD_DUPLICATE => !$addingToCart ? GrayCardDuplicateRequest::storeRules($request) : GrayCardDuplicateRequest::addToCartRules($request),
            AvailableServiceType::VEHICLE_TRANSFORMATION => !$addingToCart ? VehicleTransformationRequest::storeRules($service, $request) : VehicleTransformationRequest::addToCartRules($service, $request),
            AvailableServiceType::GLASS_ENGRAVING => !$addingToCart ? GlassEngravingRequest::storeRules($service, $request) : GlassEngravingRequest::addToCartRules($service, $request),
            default => []
        };

        if ($addingToCart){
            $rules += [
                'documents' => [Rule::requiredIf($service && $service->type->code != AvailableServiceType::SALE_DECLARATION && $service->documents()->count() > 0), 'array'],
                'documents.*.type_id' => ['required',],
                'documents.*.file' => ['required', 'file'],
            ];
        }else{
            $rules += [
                'documents' => ['nullable', 'array',],
                'documents.*.type_id' => ['required',],
                'documents.*.file' => ['required','file'],
            ];
        }

        return $rules;
    }
    public static function getDemandUpdateRuleByService(?Service $service, FormRequest $request, bool $addingToCart = false): array
    {
        $rules =  match ($service?->type?->code){
            AvailableServiceType::IMMATRICULATION_STANDARD,
            AvailableServiceType::IMMATRICULATION_PRESTIGE_NUMBER,
            AvailableServiceType::IMMATRICULATION_PRESTIGE_NUMBER_LABEL => ImmatriculationRequest::updateRules($request),
            AvailableServiceType::IMMATRICULATION_PRESTIGE_LABEL => ImmatriculationLabelRequest::updateRules($request),
            AvailableServiceType::VEHICLE_TRANSFORMATION => VehicleTransformationRequest::updateRules($request),
            AvailableServiceType::TITLE_DEPOSIT => TitleDepositRequest::updateRules($request),
            default => []
        };

        $rules += [
            'documents' => ['nullable', 'array',],
            'documents.*.type_id' => ['required',],
            'documents.*.file' => ['required','file'],
        ];

        return $rules;
    }

    public static function addCharacteristicsRule(FormRequest $request, bool $addingToCart = false): array
    {
        $rules = VehicleTransformationRequest::addCharacteristics($request);

        return $rules;
    }

}
