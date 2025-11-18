<?php

use App\Enums\VehicleCharacteristicCategoryType;

return [
    VehicleCharacteristicCategoryType::string->name => "Chaîne de caractère",
    VehicleCharacteristicCategoryType::interval->name => "Intervalle",
    VehicleCharacteristicCategoryType::numeric->name => "Numérique",
];
