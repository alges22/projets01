<?php

use Ntech\MetadataPackage\Http\Controllers\Metadata\MetaDataController;
use Illuminate\Support\Facades\Route;

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

/*
 * Models
 */

Route::get('meta-data', [MetaDataController::class,"index"]);
Route::put('meta-data', [MetaDataController::class,"update"]);
