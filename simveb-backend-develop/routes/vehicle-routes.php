<?php

use App\Http\Controllers\Vehicle\VehicleController;
use App\Http\Controllers\Vehicle\VehicleManagerController;
use App\Http\Controllers\Vehicle\VehicleOwnerController;
use App\Models\Vehicle\Vehicle;
use Illuminate\Support\Facades\Route;

Route::resource('vehicle-owners', VehicleOwnerController::class);
Route::resource('vehicles', VehicleController::class);
Route::get('get-vehicle', [VehicleManagerController::class, 'show']);
Route::get('vehicles/{vehicle}/plates', [VehicleController::class, 'getPlates']);
Route::post('store-vehicle-by-vin', [VehicleManagerController::class, 'store']);
