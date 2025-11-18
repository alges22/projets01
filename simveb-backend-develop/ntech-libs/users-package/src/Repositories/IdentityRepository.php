<?php

namespace Ntech\UserPackage\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Ntech\UserPackage\Models\Identity;

class IdentityRepository
{

    private Identity $identity;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->identity = new Identity;
    }


    /**
     * Get an element
     */
    public function get($id)
    {
        return $this->identity::query()->findOrFail($id);
    }

    /**
     * To store model
     */
    public function create($data): Builder|Model
    {
        return $this->identity::query()->create($data);
    }

    /**
     * To store model
     */
    public function updateOrCreate($data): Builder|Model
    {
        return $this->identity::query()->updateOrCreate(Arr::only($data, ['npi']),$data);
    }


    /**
     * To update model
     */
    public function update($identity, $data): Identity
    {
        $identity->update($data);

        return $identity->refresh();
    }


    public function filterIdentity($query)
    {

        if (request()->firstname)
        {
            $firstname = trim(strtolower(request()->firstname));
            $query->whereHas('identity', function ($q) use ($firstname) {
                $q->whereRaw("LOWER(firstname) LIKE ?", ["%$firstname%"]);
            });
        }

        if (request()->lastname)
        {
            $lastname = trim(strtolower(request()->lastname));
            $query->whereHas('identity', function ($q) use ($lastname) {
                $q->whereRaw("LOWER(lastname) LIKE ?", ["%$lastname%"]);
            });
        }

        if (request()->name)
        {
            $name = trim(strtolower(request()->name));
            $query->whereHas('identity', function ($q) use ($name) {
                $q->whereRaw("CONCAT(LOWER(firstname), ' ',LOWER(lastname)) LIKE ?", ["%$name%"])
                ->orWhereRaw("CONCAT(LOWER(lastname), ' ',LOWER(firstname)) LIKE ?", ["%$name%"]);
            });
        }

        if (request()->civility)
        {
            $civility = trim(strtolower(request()->civility));
            $query->whereHas('identity', function ($q) use ($civility) {
                $q->whereRaw("LOWER(civility) LIKE ?", ["%$civility%"]);
            });
        }

        if (request()->matrimonial_status)
        {
            $matrimonial_status = trim(strtolower(request()->matrimonial_status));
            $query->whereHas('identity', function ($q) use ($matrimonial_status) {
                $q->whereRaw("LOWER(matrimonial_status) LIKE ?", ["%$matrimonial_status%"]);
            });
        }

        if (request()->address)
        {
            $address = trim(strtolower(request()->address));
            $query->whereHas('identity', function ($q) use ($address) {
                $q->whereRaw("LOWER(address) LIKE ?", ["%$address%"]);
            });
        }

        if (request()->email)
        {
            $email = trim(strtolower(request()->email));
            $query->whereHas('identity', function ($q) use ($email) {
                $q->whereRaw("LOWER(email) LIKE ?", ["%$email%"]);
            });
        }

        if (request()->telephone)
        {
            $telephone = trim(strtolower(request()->telephone));
            $query->whereHas('identity', function ($q) use ($telephone) {
                $q->whereRaw("LOWER(telephone) LIKE ?", ["%$telephone%"]);
            });
        }

        if (request()->telephone_professional)
        {
            $telephone_professional = trim(strtolower(request()->telephone_professional));
            $query->whereHas('identity', function ($q) use ($telephone_professional) {
                $q->whereRaw("LOWER(telephone_professional) LIKE ?", ["%$telephone_professional%"]);
            });
        }

        if (request()->birth_place)
        {
            $birth_place = trim(strtolower(request()->birth_place));
            $query->whereHas('identity', function ($q) use ($birth_place) {
                $q->whereRaw("LOWER(birth_place) LIKE ?", ["%$birth_place%"]);
            });
        }

        if (request()->sex)
        {
            $sex = trim(strtolower(request()->sex));
            $query->whereHas('identity', function ($q) use ($sex) {
                $q->whereRaw("LOWER(sex) LIKE ?", ["%$sex%"]);
            });
        }

        if (request()->social_category)
        {
            $social_category = trim(strtolower(request()->social_category));
            $query->whereHas('identity', function ($q) use ($social_category) {
                $q->whereRaw("LOWER(social_category) LIKE ?", ["%$social_category%"]);
            });
        }

        if (request()->npi)
        {
            $npi = trim(strtolower(request()->npi));
            $query->whereHas('identity', function ($q) use ($npi) {
                $q->whereRaw("LOWER(npi) LIKE ?", ["%$npi%"]);
            });
        }

        if (request()->education_level)
        {
            $education_level = trim(strtolower(request()->education_level));
            $query->whereHas('identity', function ($q) use ($education_level) {
                $q->whereRaw("LOWER(education_level) LIKE ?", ["%$education_level%"]);
            });
        }

        if (request()->city)
        {
            $city = trim(strtolower(request()->city));
            $query->whereHas('identity', function ($q) use ($city) {
                $q->whereRaw("LOWER(city) LIKE ?", ["%$city%"]);
            });
        }

        if (request()->district)
        {
            $district = trim(strtolower(request()->district));
            $query->whereHas('identity', function ($q) use ($district) {
                $q->whereRaw("LOWER(district) LIKE ?", ["%$district%"]);
            });
        }

        if (request()->profession_id)
        {
            $profession_id = trim(strtolower(request()->profession_id));
            $query->whereHas('identity', function ($q) use ($profession_id) {
                $q->where('profession_id', $profession_id);
            });
        }

        if (request()->activity_id)
        {
            $activity_id = trim(strtolower(request()->activity_id));
            $query->whereHas('identity', function ($q) use ($activity_id) {
                $q->where('activity_id', $activity_id);
            });
        }

        if (request()->country_id)
        {
            $country_id = trim(strtolower(request()->country_id));
            $query->whereHas('identity', function ($q) use ($country_id) {
                $q->where('country_id', $country_id);
            });
        }

        return $query;
    }
}
