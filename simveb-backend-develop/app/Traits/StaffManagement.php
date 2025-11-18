<?php

namespace App\Traits;

use App\Traits\EmailArray;
use App\Traits\TelephoneArray;
use Illuminate\Validation\Rule;
use Suntech\MainService\Models\Role;
use Suntech\MainService\Models\Staff;

/**
 * Trait StaffManagement
 * @package Suntech\MainService\Traits
 */
trait StaffManagement
{
    /**
     * @param bool $required_unit
     * @return array
     */
    protected function rules($required_unit = true, $identite_id = null)
    {

        $data = [
            'firstname' => 'required',
            'lastname' => 'required',
            'sexe' => ['required'],
            'telephone' => ['required', 'array', new TelephoneArray],
            'email' => ['required', 'array', new EmailArray],
            'position_id' => 'required|exists:positions,id',
            'institution_id' => 'required|exists:institutions,id',
            'is_lead' => [Rule::in([true, false])]
        ];

        if ($required_unit) {
            $data['unit_id'] = 'required|exists:units,id';
        } else {
            $data['unit_id'] = 'exists:units,id';
        }

        if ($identite_id != null) {
            $userRepositories = app(UserRepository::class);
            $data['email'] = ['required', Rule::unique('users', 'username')->ignore($userRepositories->getUserByIdentity($identite_id)), 'array', new EmailArray];
        }
        return $data;

    }


    public function getCos($city = null)
    {
        return Staff::query()->with("identity")
            ->whereHas('identity.user', function ($query) {
                $query->whereHas('roles', function ($q) {
                    $q->where('name', Role::HEAD_OF_OPERATION);
                });
            })
            ->whereNull("city_id")
            ->when($city != null, function ($query) use ($city) {
                $query->where('city_id', $city->id);
            })
            ->get();
    }

    public function getDeliverers($city = null)
    {
        return Staff::query()->with("identity")
            ->where('city_id', $city->id)
            ->whereHas('identity.user', function ($query) {
                $query->whereHas('roles', function ($q) {
                    $q->where('name', Role::DELIVERER);
                });
            })
            ->get();
    }


}
