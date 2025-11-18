<?php

namespace Ntech\UserPackage\Rules;

use Illuminate\Contracts\Validation\Rule;
use Ntech\UserPackage\Models\Staff;

class UpdateStaffStatusRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(private readonly Staff $staff)
    {}

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return !$this->staff->identity->user->hasRole(['admin']);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "Impossible de changer le statut d'un administrateur.";
    }
}
