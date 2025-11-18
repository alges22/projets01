<?php

use Illuminate\Support\Facades\Route;
use Ntech\RequiredDocumentPackage\Http\Controllers\DocumentTypeController;
use Ntech\RequiredDocumentPackage\Http\Controllers\RequiredDocumentTypeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::apiResource('document-types', DocumentTypeController::class);
Route::resource('required-document-types', RequiredDocumentTypeController::class)->except(['edit']);
