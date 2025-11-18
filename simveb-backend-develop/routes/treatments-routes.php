<?php

use App\Http\Actions\Demand\AssignDemandToCenterAction;
use App\Http\Actions\Demand\AssignDemandToInterpolAction;
use App\Http\Actions\Demand\AssignDemandToInterpolStaffAction;
use App\Http\Actions\Demand\AssignDemandToServiceAction;
use App\Http\Actions\Demand\AssignDemandToStaffAction;
use App\Http\Actions\Demand\CloseDemandAction;
use App\Http\Actions\PrintImmatriculationAction;
use App\Http\Actions\RejectTreatmentAction;
use App\Http\Actions\SuspendTreatmentAction;
use App\Http\Actions\ValidateTreatmentAction;
use App\Http\Actions\ValidateTreatmentByInterpolAction;
use App\Http\Actions\VerifyDemandAction;
use App\Http\Controllers\Admin\Treatment\TreatmentController;
use App\Http\Controllers\Client\Order\PrintOrderController;
use Illuminate\Support\Facades\Route;

Route::get('treatments/create',[TreatmentController::class,'create'])->name('treatment.create');
Route::post('assign-demand-to-center', AssignDemandToCenterAction::class)->name('assign.demand.center');
Route::post('assign-demand-to-service', AssignDemandToServiceAction::class)->name('assign.demand.service');
Route::post('assign-demand-to-interpol', AssignDemandToInterpolAction::class)->name('assign.demand.interpol');
Route::post('assign-demand-to-staff', AssignDemandToStaffAction::class)->name('assign.demand.staff');
Route::post('print-or-emit-order', PrintImmatriculationAction::class)->name('print.emit.order');
Route::post('assign-demand-to-interpol-staff', AssignDemandToInterpolStaffAction::class)->name('assign.demand.interpol.staff');
Route::post('verify-demand', VerifyDemandAction::class)->name('demand.verify');
Route::post('reject-demand', RejectTreatmentAction::class)->name('demand.reject');
Route::post('validate-demand', ValidateTreatmentAction::class)->name('demand.validate');
Route::post('validate-demand-interpol', ValidateTreatmentByInterpolAction::class)->name('demand.validate.interpol');
Route::post('suspend-demand', SuspendTreatmentAction::class)->name('demand.suspend');
Route::post('emit-print-order', [PrintOrderController::class, 'store'])->name('emit-print-order');
Route::post('close-demand', CloseDemandAction::class)->name('demand.close');
