<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleOwnerResource extends JsonResource
{
    public function __construct($resource, private string $source = 'database')
    {
        parent::__construct($resource);
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        if (isset($this->identity->npi) || isset($this['npi'])) {
            if ($this->source == 'database') {
                $data = [
                    'email' => $this->identity->email,
                    'firstname' => $this->identity->firstname,
                    'lastname' => $this->identity->lastname,
                    'telephone' => $this->identity->telephone,
                    'birth_place' => $this->identity->birth_place,
                    'birth_date' => $this->identity->birthdate,
                    "origin_country" => $this->country?->name,
                    'gender' => $this->identity->gender,
                    'bfu' => $this->bfu,
                    'address' => $this->identity->address,
                    'npi' => $this->identity->npi,
                    'ifu' => $this->identity->ifu,
                    'dataOriginType' => 'Persons'
                ];
            } else {
                $data = [
                    'email' => $this['email'],
                    'firstname' => $this['firstname'],
                    'lastname' => $this['lastname'],
                    'telephone' => $this['telephone'],
                    'birth_place' => $this['birth_place'],
                    'birth_date' => $this['birth_date'],
                    "origin_country" => $this['origin_country'],
                    'gender' => $this['gender'],
                    'bfu' => $this['bfu'] ?? null,
                    'address' => $this['address'],
                    'npi' => $this['npi'],
                    'ifu' => $this['ifu'] ?? null,
                    'dataOriginType' => 'Persons'
                ];
            }
        } else {
            if ($this->source == 'database') {
                $data = [
                    'email' => $this->email,
                    'name' => $this->name,
                    'telephone' => $this->telephone,
                    'bfu' => $this->bfu,
                    'address' => $this->seat,
                    'ifu' => $this->ifu,
                    'dataOriginType' => 'Companies'
                ];
            } else {
                $data = [
                    'email' => $this['email'],
                    'name' => $this['name'],
                    'telephone' => $this['telephone'],
                    'bfu' => isset($this['bfu']) ? $this['bfu'] : null,
                    'ifu' => $this['ifu'],
                    'dataOriginType' => 'Companies'
                ];
            }
        }

        return $data;
    }
}
