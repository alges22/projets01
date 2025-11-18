<?php

namespace App\Rules;

use App\Enums\Status;
use App\Models\Config\ReservedPlateNumber;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Translation\PotentiallyTranslatedString;

class NumberIsAlreadyReservedRule implements ValidationRule
{
    public function __construct(private readonly FormRequest $request)
    {
    }

    /**
     * Run the validation rule.
     *
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $isReserved = false;

        // on reservation validation or invalidation
        if(array_key_exists('reserved_plate_number_id', $this->request->all())) {
            $reservedPlateNumber = ReservedPlateNumber::find($value);
            if ($reservedPlateNumber->status == Status::validated->name) {
                $isReserved = true;
            }
        } else {
            // creation or update of a reservation

            // specific number
            if($this->request->filled('numeric_label')) {

                // check existing specific number
                $reservedPlateNumber = ReservedPlateNumber::where('numeric_label', $this->request->numeric_label)
                                                        ->where('alphabetic_label', $this->request->alphabetic_label)
                                                        ->whereIn('status', [Status::validated->name]);
                if($reservedPlateNumber->count() > 0) {
                    $isReserved = true;
                }

                if(!$isReserved) {
                    // check if specific number matches an existing range
                    $reservedPlateNumber = ReservedPlateNumber::where('min', '<=', $this->request->numeric_label)
                                                            ->where('max', '>=', $this->request->numeric_label)
                                                            ->where('alphabetic_label', $this->request->alphabetic_label)
                                                            ->whereIn('status', [Status::validated->name]);
                    if($reservedPlateNumber->count() > 0) {
                        $isReserved = true;
                    }
                }

                if(!$isReserved) {
                    // check existing serie
                    $reservedPlateNumber = ReservedPlateNumber::whereNull('min')
                                                            ->whereNull('max')
                                                            ->whereNull('numeric_label')
                                                            ->where('alphabetic_label', $this->request->alphabetic_label)
                                                            ->whereIn('status', [Status::validated->name]);
                    if($reservedPlateNumber->count() > 0) {
                        $isReserved = true;
                    }
                }
            }

            // range of numbers
            if ($this->request->filled('min') && $this->request->filled('max')){
                // check if range is included in an existing range
                $reservedPlateNumber = ReservedPlateNumber::where('min', '<=', $this->request->min)
                                                        ->where('max', '>=', $this->request->max)
                                                        ->where('alphabetic_label', $this->request->alphabetic_label)
                                                        ->whereIn('status', [Status::validated->name]);
                if($reservedPlateNumber->count() > 0) {
                    $isReserved = true;
                }

                if(!$isReserved) {
                    // check if range is included in an existing serie
                    $reservedPlateNumber = ReservedPlateNumber::where('alphabetic_label', $this->request->alphabetic_label)
                                                            ->whereIn('status', [Status::validated->name]);
                    if($reservedPlateNumber->count() > 0) {
                        $isReserved = true;
                    }
                }
            }

            // serie of numbers
            if ($this->request->filled('alphabetic_label') &&
                is_null($this->request->filled('max')) &&
                is_null($this->request->filled('min')) &&
                is_null($this->request->filled('numeric_label'))){

                    // check if the serie is defined already
                    $reservedPlateNumber = ReservedPlateNumber::where('alphabetic_label', $this->request->alphabetic_label)
                                                            ->whereIn('status', [Status::validated->name]);
                    if($reservedPlateNumber->count() > 0) {
                        $isReserved = true;
                    }
            }

        }


        if ($isReserved){
            $fail("Ce numéro est déjà reservé");
        }
    }
}
