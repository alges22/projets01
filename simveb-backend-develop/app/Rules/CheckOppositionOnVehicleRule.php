<?php

namespace App\Rules;

use App\Models\Opposition;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Enums\Status;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CheckOppositionOnVehicleRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $vehicles = request()->vehicles;

        $existingVehicles = Opposition::whereHas('vehicles', function($query) use ($vehicles) {
            $query->whereIn('id', $vehicles)->whereIn('status', [Status::opposition_emitted->name, Status::clerk_validated->name,
            Status::opposition_lifted_emitted, Status::clerk_rejected->name, Status::judge_rejected->name, Status::opposition_emitted->name,]);
        })->exists();

        if ($existingVehicles) {
            $fail("Impossible, au moins un véhicule en cours de traitement d'opposition");
        }
    }
}
