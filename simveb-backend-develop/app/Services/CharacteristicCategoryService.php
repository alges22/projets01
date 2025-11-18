<?php

namespace App\Services;

use App\Exceptions\UnexceptedErrorException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CharacteristicCategoryService
{
    public function fetchCharacteristicFields($formatReponse = false)
    {
        try {
            $retrieveVechicles = Http::get(config('app.sandbox_host') . '/vehicles');
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error($e);
            throw new UnexceptedErrorException;
        }

        if ($retrieveVechicles->failed()) {
            Log::error($retrieveVechicles->json());

            if ($formatReponse) {
                return [false, ['message' => $retrieveVechicles->json(), 'code' => $retrieveVechicles->getStatusCode()]];
            }
            throw new UnexceptedErrorException;
        } else {

            $randomVehicle = $retrieveVechicles->json()[array_rand($retrieveVechicles->json())];
            $fields = array_keys($randomVehicle);

            if ($formatReponse) {
                return [true, ['fields' => $fields]];
            }
            return $fields;
        }
    }
}
