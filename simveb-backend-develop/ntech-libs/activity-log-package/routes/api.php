<?php

use Ntech\ActivityLogPackage\Http\Controllers\ActivityLog\ActivityLogController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API !
|
*/
Route::get('activity-logs', [ActivityLogController::class,"index"])->name("activity-logs.index")->middleware('auth');
Route::get('activity-logs/{id}', [ActivityLogController::class,"show"])->name("activity-logs.details")->middleware('auth');
