<?php

use App\Http\Actions\ApproveCartAction;
use App\Http\Actions\GetGmaVehicleStatsAction;
use App\Http\Actions\RejectGmaVehicleAction;
use App\Http\Actions\RejectGmdVehicleAction;
use App\Http\Actions\ValidateGmaVehicleAction;
use App\Http\Actions\ValidateGmdVehicleAction;
use App\Http\Controllers\MotorcycleController;
use App\Http\Controllers\GovVehicleController;
use App\Http\Controllers\GovVehicleImportExportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReformDeclarationController;
use App\Http\Controllers\AuctionSaleDeclarationController;
use App\Http\Controllers\AuctionSaleVehicleController;
use App\Http\Controllers\GmaVehicleController;
use App\Http\Controllers\GmaVehicleImportExportController;
use App\Http\Controllers\GmdVehicleController;
use App\Http\Controllers\GmdVehicleImportExportController;

//Gov Vehicles
Route::resource('gov-vehicles', GovVehicleController::class);
Route::post('import-gov-vehicles', [GovVehicleImportExportController::class, 'store'])->name('import-gov-vehicles.store');

//Gma Vehicles
Route::resource('gma-vehicles', GmaVehicleController::class);
Route::post('import-gma-vehicles', [GmaVehicleImportExportController::class, 'store'])->name('import-gma-vehicles.store');
Route::post('validate-gma-vehicles', ValidateGmaVehicleAction::class);
Route::post('reject-gma-vehicles', RejectGmaVehicleAction::class);
Route::post('get-gma-vehicle-stats', GetGmaVehicleStatsAction::class);

// Gmd Vehicles
Route::resource('gmd-vehicles', GmdVehicleController::class);
Route::post('validate-gmd-vehicles', ValidateGmdVehicleAction::class);
Route::post('reject-gmd-vehicles', RejectGmdVehicleAction::class);
Route::post('import-gmd-vehicles', [GmdVehicleImportExportController::class, 'store'])->name('import-gmd-vehicles.store');


//Reform Declaration
Route::get('reform-declarations/{reform_declaration}/generate-certificate', [ReformDeclarationController::class,'generateCertificate'])->name('reform-declarations.generate-certificate');
Route::resource('reform-declarations', ReformDeclarationController::class);

Route::get('auction-sale-declarations/show-by-reference/{reference}', [AuctionSaleDeclarationController::class, 'showByReference'])->name('auction-sale-declarations.show-by-reference');
Route::get('auction-sale-declarations/{auction_sale_declaration}/generate-certificate', [AuctionSaleDeclarationController::class, 'generateCertificate'])->name('auction-sale-declarations.genete-certificate');

//Auction Sale Vehicle
Route::resource('auction-sale-vehicles', AuctionSaleVehicleController::class)->only(['show', 'update', 'destroy']);

Route::put('auction-sale-declarations/{auction_sale_declaration}/add-vehicle', [AuctionSaleDeclarationController::class, 'addVehicle'])->name('auction-sale-declarations.add-vehicle');
Route::put('auction-sale-declarations/{auction_sale_declaration}/remove-vehicle', [AuctionSaleDeclarationController::class, 'removeVehicle'])->name('auction-sale-declarations.remove-vehicle');
Route::put('auction-sale-declarations/{auction_sale_declaration}/add-official', [AuctionSaleDeclarationController::class, 'addOfficial'])->name('auction-sale-declarations.add-official');
Route::put('auction-sale-declarations/{auction_sale_declaration}/remove-official', [AuctionSaleDeclarationController::class, 'removeOfficial'])->name('auction-sale-declarations.remove-official');
Route::get('auction-sale-declarations/stats', [AuctionSaleDeclarationController::class, 'stats'])->name('auction-sale-declarations.stats');
Route::resource('auction-sale-declarations', AuctionSaleDeclarationController::class)->except(['edit']);


Route::post('approve-cart', ApproveCartAction::class)->name('cart.approved');

//Car Dealer Vehicle
Route::apiResource('motorcycles', MotorcycleController::class);
Route::get('motorcycle/file-format', [MotorcycleController::class, 'fileFormat'])->name('motorcycle.file-format');
Route::post('motorcycle/import', [MotorcycleController::class, 'import'])->name('motorcycle.import');
