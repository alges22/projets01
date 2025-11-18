<?php

namespace App\Rules;

use App\Enums\Status;
use App\Models\Plate\PlateOrder;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CanConfirmPlateOrderRule implements ValidationRule
{
    public function __construct(private readonly PlateOrder|null $plateOrder) {}

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->plateOrder) {
            if ($this->plateOrder->seller_id) {
                if (getOnlineProfile()->institution_id != $this->plateOrder->seller_id) {
                    $fail('Vous ne pouvez pas effectuer cette action sur cette commande.');
                }
            }

            if ($this->plateOrder->status != Status::pending->name) {
                $fail('Cette demande est déjà traitée.');
            }
        }
    }
}
