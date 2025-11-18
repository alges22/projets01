<?php

use App\Http\Controllers\Admin\Demand\DemandController;
use App\Http\Controllers\Immatriculation\ImmatriculationController;
use App\Http\Controllers\Immatriculation\ImmatriculationFormatController;
use App\Http\Controllers\Immatriculation\ImmatriculationNumberController;
use Illuminate\Support\Facades\Route;

Route::resource('immatriculation-formats', ImmatriculationFormatController::class);
Route::resource('immatriculation-demands', DemandController::class)->except(['create', 'store']);
Route::resource('immatriculations', ImmatriculationController::class);
Route::get('generate-immatriculation-number',[ImmatriculationNumberController::class,'generateNumber'])->name('generate.number');
Route::post('anatt-control-immatriculation-demand', [DemandController::class, 'anattValidation'])->name('im-demand.anatt-control');
