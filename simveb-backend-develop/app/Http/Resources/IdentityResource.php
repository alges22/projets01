<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IdentityResource extends JsonResource
{
    public function __construct($resource, private string $source = 'database')
    {
        parent::__construct($resource);
        $this->source = $source;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($this->source == 'database') {
            $data = [
                'email' => $this->email,
                'firstname' => $this->firstname,
                'lastname' => $this->lastname,
                'telephone' => $this->telephone,
                'birth_place' => $this->birth_place,
                'birth_date' => $this->birthdate,
                "origin_country" => $this->country?->name,
                'gender' => $this->gender,
                'bfu' => $this->vehicleOwner?->bfu,
                'address' => $this->address,
                'npi' => $this->npi,
                'ifu' => $this->ifu,
            ];
        } else {
            $data = [
                'email' => $this['email'] ?? null,
                'firstname' => $this['firstname'],
                'lastname' => $this['lastname'],
                'telephone' => $this['phone_number_indicatif'] . $this['phone_number'],
                'birth_place' => $this['birth_place'],
                'birth_date' => $this['birthdate'],
                "origin_country" => $this['residence_country_code'],
                'gender' => $this['sexe'],
                'address' => $this['residence_address'],
                'npi' => $this['npi'],
                'ifu' => $this['ifu'] ?? null,
                'bfu' => $this['bfu'] ?? null,
            ];
        }

        return $data;
    }
}
