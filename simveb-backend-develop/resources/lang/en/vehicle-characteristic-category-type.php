<?php

use App\Enums\VehicleCharacteristicCategoryType;

return [
    VehicleCharacteristicCategoryType::string->name => "String of characters",
    VehicleCharacteristicCategoryType::interval->name => "Interval",
    VehicleCharacteristicCategoryType::numeric->name => "Numeric",
];
