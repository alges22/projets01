<?php


use App\Http\Actions\Demand\ValidateCartAction;
use App\Http\Controllers\CertificateController;
use App\Http\Actions\Demand\AddDemandToCartAction;
use App\Http\Controllers\Client\Demand\CartController;
use App\Http\Controllers\Client\Demand\OrderController;
use App\Http\Controllers\Client\Demand\DemandController;
use App\Http\Controllers\Client\Order\InvoiceController;
use App\Http\Controllers\Client\Service\ServiceController;
use App\Http\Controllers\Client\Demand\DemandOtpController;
use App\Http\Controllers\Client\Order\SubmitOrderController;
use App\Http\Controllers\Client\Demand\PendingDemandController;
use App\Http\Controllers\Client\Vehicle\BoughtVehicleController;
use App\Http\Controllers\Client\Vehicle\OnlineVehicleController;
use App\Http\Controllers\VehicleTitle\SaleDeclarationController;
use App\Http\Controllers\Client\Demand\DemandAttachmentController;
use App\Http\Controllers\Client\Suggestion\SuggestImmLabelController;
use App\Http\Controllers\VehicleTransformationController;
use Illuminate\Support\Facades\Route;

Route::name('client.')
    ->prefix('client')
    ->group(function () {
        Route::get('services',[ServiceController::class,'index'])->name('services.index');
        Route::get('services/{service_code}',[ServiceController::class,'show'])->name('services.details');
    });

Route::name('client.')
    ->prefix('client')
    ->middleware(['auth:api', 'space.access'])
    ->group(function () {
        //Demands routes
        Route::get('demands/verify-vehicle-situation/{vin}', [DemandController::class, 'verifyVehicleSituation'])->name('verify.vehicle');
        Route::get('get-pending-demands',[PendingDemandController::class,'index'])->name('pending-demands.index');
        Route::get('demands/{demand}', [DemandController::class, 'show'])->name('demands.show');
        Route::get('demands/create/{service}',[DemandController::class,'create'])->name('demands.create');
        Route::get('demands/edit/{demand}',[DemandController::class,'edit'])->name('demands.edit');
        Route::put('demands/attachments-update/{demand}', [DemandAttachmentController::class, 'update'])->name('demands.attachment.update');
        Route::resource('demands',DemandController::class)->only(['index', 'store', 'update']);

        Route::get('get-vehicles',[OnlineVehicleController::class,'index']);
        Route::get('get-bought-vehicles',[BoughtVehicleController::class,'index']);
        Route::post('add-demand-to-cart', AddDemandToCartAction::class)->name('demands.to.cart');
        Route::post('validate-cart', ValidateCartAction::class)->name('validate.cart');
        Route::get('cart', [CartController::class, 'index'])->name('get.cart');
        Route::delete('cart-remove-demand/{demand}', [CartController::class, 'removeItem'])->name('remove.item.cart');
        Route::delete('empty-cart', [CartController::class, 'emptyCart'])->name('empty.cart');
        Route::post('send-demand-otp',[DemandOtpController::class,'store'])->name('send.demand.otp');
        Route::put('verify-demand-otp',[DemandOtpController::class,'update'])->name('verify.demand.otp');

        //Orders
        Route::get('orders/{order}',[OrderController::class,'show'])->name('orders.show');
        Route::get('orders',[OrderController::class,'index'])->name('orders.index');
        Route::post('submit-order', [SubmitOrderController::class,'store'])->name('submit.order');
        Route::post('invoices/{order}/generate', [InvoiceController::class, 'generate'])->name('invoice.generate');

        //number suggestions
        Route::post('check-label',[SuggestImmLabelController::class,'checkLabelExist'])->name('check.label');
        Route::post('check-number',[SuggestImmLabelController::class,'checkNumberExist'])->name('check.number');
        Route::post('suggest-numbers',[SuggestImmLabelController::class,'suggestNumbers'])->name('suggest.numbers');

        //certificate routes
        Route::get('certificates',[CertificateController::class,'index'])->name('certificate.index');
        Route::get('certificates/{certificate}', [CertificateController::class, 'show']);

        //Sale Declaration
        Route::get('sale-declarations/{reference}', [SaleDeclarationController::class, 'show']);

        // Vehicle Transformation
        Route::post('add-characteristics', [VehicleTransformationController::class, 'store']);
        Route::delete('characteristic/{transformationChar}', [VehicleTransformationController::class, 'destroy']);
    });


