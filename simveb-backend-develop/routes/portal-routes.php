<?php


use App\Http\Actions\SubmitPlateTransformationAction;
use App\Http\Actions\SubmitPrestigeLabelImmatriculationAction;
use App\Http\Actions\SubmitVehicleAdministrativeStatusAction;
use App\Http\Controllers\Admin\Treatment\PlateTransformationController;
use App\Http\Controllers\Client\Demand\DemandController;
use App\Http\Controllers\Portal\ImmatriculationController;
use App\Http\Controllers\Portal\PortalServiceController;
use App\Http\Controllers\Portal\VehicleAdministrativeStatusController;
use App\Http\Controllers\VehicleTitle\PrestigeLabelImmatriculationController;
use Illuminate\Support\Facades\Route;


Route::name('portal.')
    ->prefix('portal')
    ->group(function () {
        //services
        Route::get('service-types', [PortalServiceController::class, 'serviceTypes'])->name('service-types');

        Route::get('services', [PortalServiceController::class, 'services'])->name('services');
        Route::get('services/{service_id}', [PortalServiceController::class, 'service'])->name('services.show');

        Route::post('transactions', [PortalServiceController::class, 'createTransaction'])->name('create.transactions');

        //immatriculation
        Route::get('immatriculation-search', [ImmatriculationController::class, 'searchImmatriculation'])->name('immatriculation.search');
        Route::get('demand/{immatriculation_demand}', [ImmatriculationController::class, 'showDemand'])->name('demand.details');
        Route::get('immatriculation/{immatriculation}', [ImmatriculationController::class, 'showImmatriculation'])->name('immatriculation.details');

        //Vehicle administrative status
        Route::get('search-declarant', [VehicleAdministrativeStatusController::class, 'searchDeclarant']);
        Route::get('search-vehicle', [VehicleAdministrativeStatusController::class, 'searchVehicle']);

        Route::post('vehicle-administrative-status', [VehicleAdministrativeStatusController::class, 'store'])->name('vehicle-administrative-status.store');
        Route::get('vehicle-administrative-status-demands/{declarer}', [VehicleAdministrativeStatusController::class, 'getDemandsByDeclarer'])->name('vehicle-administrative-status.index');
        Route::get('vehicle-administrative-status/{vehicle_administrative_status}', [VehicleAdministrativeStatusController::class, 'show'])->name('vehicle-administrative-status.show');
        Route::put('vehicle-administrative-status/{vehicle_administrative_status}', [VehicleAdministrativeStatusController::class, 'update'])->name('vehicle-administrative-status.update');
        Route::delete('vehicle-administrative-status/{vehicle_administrative_status}', [VehicleAdministrativeStatusController::class, 'destroy'])->name('vehicle-administrative-status.destroy');
        Route::post('submit-vehicle-status-demands', SubmitVehicleAdministrativeStatusAction::class);

        //prestige label immatriculation
        Route::post('prestige-label-immatriculation-demand', [PrestigeLabelImmatriculationController::class, 'store'])->name('prestige-label-immatriculation-demand.store');
        Route::get('prestige-label-immatriculation-demands/{prestige_label_immatriculation}', [PrestigeLabelImmatriculationController::class, 'show'])->name('prestige-label-immatriculation-demand.show');
        Route::post('prestige-label-immatriculation-demand/{prestige_label_immatriculation}', [PrestigeLabelImmatriculationController::class, 'update'])->name('prestige-label-immatriculation-demand.update');

        Route::post('submit-prestige-label-immatriculations', SubmitPrestigeLabelImmatriculationAction::class);

        //plate transformation

        Route::post('plate-transformation-demand', [PlateTransformationController::class, 'store'])->name('plate-transformation-demand.store');
        Route::get('plate-transformation-demands/{plate_transformation}', [PlateTransformationController::class, 'show'])->name('plate-transformation-demand.show');
        Route::post('plate-transformation-demand/{plate_transformation}', [PlateTransformationController::class, 'update'])->name('plate-transformation-demand.update');

        Route::post('submit-plate-transformations', SubmitPlateTransformationAction::class);
    });

Route::group(['middleware' => ['auth:api', 'space.access']], function () {
    //Demands routes
    Route::get('demands/create/{service}', [DemandController::class, 'create'])->name('demands.create');
    Route::post('demands', [DemandController::class, 'store'])->name('demands.store');
});
