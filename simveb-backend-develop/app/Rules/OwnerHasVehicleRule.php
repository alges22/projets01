<?php

namespace App\Rules;

use App\Models\Auth\Profile;
use App\Models\Institution\Institution;
use App\Models\Vehicle\Vehicle;
use App\Models\Vehicle\VehicleOwner;
use App\Enums\LegalStatusEnum;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Ntech\UserPackage\Models\Identity;

class OwnerHasVehicleRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $institution = Institution::query()->where('ifu', $value)->first();
        $institutions = [];

        switch(strlen($value)) {
            case 10:
                $identity = Identity::where('npi', $value)->first();
                $ownerData = VehicleOwner::query()->where('identity_id', $identity?->id)->first();
                if (!$ownerData) {
                    $fail(__('Ce NPI ne dispose actuellement d\'aucun véhicule immatriculé ou non.'));
                }

                if (isset(request()->vehicles)){
                    foreach (request()->vehicles as $vehicle) {
                        $car = Vehicle::where([
                            ['id', $vehicle],
                            ['owner_id', $ownerData?->id]
                        ])->first();

                        if (!$car) $fail(__('Au moins un véhicule n\'appartient pas à ce NPI'));
                    }
                }
                break;

            case 13:
                if (request()->vehicles) {
                    foreach (request()->vehicles as $vehicle) {
                        $vehicle = Vehicle::with('owner')->find($vehicle);

                        if ($vehicle->owner ?->identity_id === null && $vehicle->owner->legal_status === LegalStatusEnum::physical->name) $fail(__('Au moins un véhicule appartient à une personne physique'));

                    if ($vehicle) {
                        $profile = Profile::where('id', $vehicle ?->owner ?->profile_id)->first();
                        $relatedProfile = Profile::where([['user_id', $profile ?->user_id],['institution_id', $institution->id]])->first();

                        if (!$relatedProfile) $fail(__('Au moins un véhicule n\'appartient pas à cette compagnie'));

                        $institutions[] = $relatedProfile ?->institution_id;
                    }
                }

                    if (empty($institutions)) {
                        $fail(__('Ce IFU ne dispose actuellement d\'aucun véhicule immatriculé ou non.'));
                    }

                    if (array_unique($institutions) !== [$institution->id]) {
                        $fail(__('Au moins un véhicule n\'appartient pas à cette compagnie'));
                    }
                }
                break;

            default:
                $fail(__('NPI ou IFU invalide.'));
                break;
        }
    }
}
