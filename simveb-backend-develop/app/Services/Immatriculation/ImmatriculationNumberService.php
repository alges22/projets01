<?php

namespace App\Services\Immatriculation;

use App\Enums\Status;
use App\Exceptions\ZoneNotFoundException;
use App\Models\Config\ReservedPlateNumber;
use App\Models\Config\Town;
use App\Models\Immatriculation\FormatComponent;
use App\Models\Immatriculation\Immatriculation;
use App\Models\Immatriculation\ImmatriculationFormat;
use App\Models\Immatriculation\LastImmatriculation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ImmatriculationNumberService
{
    public static function getNextLetterSuit($input)
    {

        $length = strlen($input);
        $carry = 1; // Initialize carry to add to the last character
        // Start from the last character and move towards the first
        for ($i = $length - 1; $i >= 0; $i--) {
            $asciiValue = ord($input[$i]); // Get ASCII value of the character
            // Increment ASCII value by carry
            $asciiValue += $carry;
            // Reset carry if ASCII value exceeds 'z'
            if ($asciiValue > ord('z')) {
                $asciiValue = ord('a');
                $carry = 1;
            } else {
                $carry = 0;
            }
            // Update the character in the string
            $input[$i] = chr($asciiValue);
            // If no carry, we've successfully updated the string
            if ($carry == 0) {
                break;
            }
        }
        // If carry is still 1, we need to add a new character to the beginning
        if ($carry == 1) {
            $input = 'a' . $input;
        }

        return $input;
    }

    // TODO: optimise algo
    public function generateNewNumber(ImmatriculationFormat $immatriculationFormat, Town $town, ?string $desiredNumber = null)
    {
        DB::beginTransaction();
        try {
            $numberIsReserved = false;
            $lastImmatriculationNumber = getLastImmNumber($immatriculationFormat);
            if ($lastImmatriculationNumber){
                $referenceNumber = $lastImmatriculationNumber->numeric_label;
            }else{
                $referenceNumber = 0;
            }
            do {
                $alphabetic_label = $this->generateAlpha($immatriculationFormat);
                $numeric_label = $this->generateNumeric($immatriculationFormat, $referenceNumber, $desiredNumber);
                $prefix = $immatriculationFormat->components()->where('code', 'prefix')->first()?->pivot->value ?? '';
                $country_code = $immatriculationFormat->components()->where('code', 'country_code')->first()?->default_value ?
                    $immatriculationFormat->components()->where('code', 'country_code')->first()?->default_value
                    : 'BJ';
                $zone = $town->getZone()->code;

                $reservedNumber = $this->getReservationFromNumber($alphabetic_label, $numeric_label);

                if ($reservedNumber) {
                    if (!$reservedNumber->alphabetic_label) {
                        $numberIsReserved = true;
                    } else {
                        if ($reservedNumber->alphabetic_label == $alphabetic_label) {
                            $numberIsReserved = true;
                        }
                    }
                }
                $numberIsAlreadyTaken = Immatriculation::query()
                    ->where([
                        FormatComponent::NUMERIC_LABEL => $numeric_label,
                        FormatComponent::ALPHABETIC_LABEL => $alphabetic_label,
                        FormatComponent::PREFIX => $prefix,
                    ])
                    ->exists();
                if ($numberIsReserved || $numberIsAlreadyTaken){
                    $referenceNumber++;
                }
            } while ($numberIsReserved);

            $format = [];
            for ($i = 0; $i < count($immatriculationFormat->components); $i++) {
                $selectedComponent = $immatriculationFormat->components()->wherePivot('position', ($i + 1))->first();
                $format[$i] = ${$selectedComponent->code};
            }
            if (!$desiredNumber) {
                $this->updateLastImmatriculationNumber([
                    'prefix' => $prefix,
                    'alphabetic_label' => $alphabetic_label,
                    'zone' => $zone,
                    'numeric_label' => $numeric_label,
                    'country_code' => $country_code,
                    'vehicle_category_id' => $immatriculationFormat->vehicle_category_id
                ]);
            }

            DB::commit();
            return [
                'format' => $format,
                'components' => [
                    'prefix' => $prefix,
                    'alphabetic_label' => $alphabetic_label,
                    'zone' => $zone,
                    'numeric_label' => $numeric_label,
                    'country_code' => $country_code,
                ]];

        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            if ($exception instanceof ZoneNotFoundException) {
                abort(ResponseAlias::HTTP_BAD_REQUEST, $exception->getMessage());
            } else {
                abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }

    private function generateAlpha(ImmatriculationFormat $immatriculationFormat): string
    {
        $lastImmatriculation = $this->getLastGeneratedNumber($immatriculationFormat);
        if (!$lastImmatriculation) {
            $alphabeticLength = $immatriculationFormat->components()->where('code', 'alphabetic_label')->first()?->pivot->length ?
                $immatriculationFormat->components()->where('code', 'alphabetic_label')->first()?->pivot->length :
                $immatriculationFormat->components()->where('code', 'alphabetic_label')->first()?->default_length;
            $alphabetic_label = Str::repeat('A', $alphabeticLength);
        } else {
            if ($lastImmatriculation->numeric_label == 9999) {
                $alphabetic_label = $this::getNextLetterSuit($lastImmatriculation->alphabetic_label);
            } else {
                $alphabetic_label = $lastImmatriculation->alphabetic_label;
            }
        }

        return $alphabetic_label;
    }

    /**
     * @param ImmatriculationFormat $immatriculationFormat
     * @param int $numberReference, number from which the nex should be generated
     * @param string|null $desiredNumber
     * @return string
     */
    private function generateNumeric(ImmatriculationFormat $immatriculationFormat, int $numberReference, ?string $desiredNumber = null): string
    {
        if ($desiredNumber) {
            $numeric_label = $desiredNumber;
        } else {
            $numeric_label = $numberReference + 1;
            $numericLength = $immatriculationFormat->components()->where('code', 'numeric_label')->first()?->pivot->length ?
                $immatriculationFormat->components()->where('code', 'numeric_label')->first()?->pivot->length :
                $immatriculationFormat->components()->where('code', 'numeric_label')->first()?->default_length;

            if (Str::length($numeric_label) < $numericLength) {
                $append = Str::repeat("0", ($numericLength - Str::length($numeric_label)));
                $numeric_label = "$append$numeric_label";
            }
        }

        return $numeric_label;
    }

    public function getLastGeneratedNumber($immatriculationFormat = null): object|null
    {
        return isset($immatriculationFormat) ?
            LastImmatriculation::query()
                ->where('vehicle_category_id', $immatriculationFormat?->vehicle_category_id)
                ->first() :
            LastImmatriculation::query()
                ->first();
    }

    public function updateLastImmatriculationNumber($data): void
    {
        if ($immatriculation = LastImmatriculation::query()->where('vehicle_category_id', $data['vehicle_category_id'])->first()){
            $immatriculation->update($data);
        }else{
            LastImmatriculation::query()->create($data);
        }
    }

    public function getReservationFromNumber($alphabetic_label, $numeric_label)
    {
        $reservation = null;
        $isReserved = null;
        // check existing specific number
        $reservedPlateNumber = ReservedPlateNumber::where('numeric_label', $numeric_label)
                                                ->where('alphabetic_label', $alphabetic_label)
                                                ->whereIn('status', [Status::validated->name])
                                                ->get();
        if($reservedPlateNumber->count() > 0) {
            $isReserved = true;
            $reservation = $reservedPlateNumber->first();
        }

        if(!$isReserved) {
            // check if specific number matches an existing range
            $reservedPlateNumber = ReservedPlateNumber::where('alphabetic_label', $alphabetic_label)
                                                    ->where('min', '<=', $numeric_label)
                                                    ->where('max', '>=', $numeric_label)
                                                    ->whereIn('status', [Status::validated->name])
                                                    ->get();
            if($reservedPlateNumber->count() > 0) {
                $isReserved = true;
                $reservation = $reservedPlateNumber->first();
            }
        }

        if(!$isReserved) {
            // check existing serie
            $reservedPlateNumber = ReservedPlateNumber::whereNull('min')
                                                    ->whereNull('max')
                                                    ->whereNull('numeric_label')
                                                    ->where('alphabetic_label', $alphabetic_label)
                                                    ->whereIn('status', [Status::validated->name])
                                                    ->get();
            if($reservedPlateNumber->count() > 0) {
                $isReserved = true;
                $reservation = $reservedPlateNumber->first();
            }
        }
        return $reservation;
    }
}
