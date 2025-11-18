<?php

use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\ExternalServiceTestController;
use App\Http\Controllers\UserController;
use Ntech\UserPackage\Http\Controllers\Auth\PasswordExpiredResetController;
use Ntech\UserPackage\Http\Controllers\Auth\PasswordResetController;
use Illuminate\Support\Facades\Route;
use Ntech\UserPackage\Http\Controllers\Auth\AuthController;
use Ntech\UserPackage\Http\Controllers\Auth\LoginController;
use Ntech\UserPackage\Http\Controllers\Auth\RegisterController;
use Ntech\UserPackage\Http\Controllers\Auth\RegistrationSearchController;
use Ntech\UserPackage\Http\Controllers\Role\PermissionController;
use Ntech\UserPackage\Http\Controllers\Role\RoleController;
use Ntech\UserPackage\Http\Controllers\Staff\StaffController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|


/**
 * Password Reset
 */
Route::name('forgot.password')->post('/forgot-password', [PasswordResetController::class,'create']);
Route::name('reset.password')->get('/forgot-password/{token}', [PasswordResetController::class,'find']);
Route::name('reset.password.post')->post('/reset-password', [PasswordResetController::class,'reset']);
Route::name('reset.password.expired')->put('/reset-password-expired', [PasswordExpiredResetController::class,'update']);

/**
 * Authentication
 * User Login / Logout
 */
Route::post("/logout",[AuthController::class,"logout"])->name("logout.user");



Route::group(['middleware' => 'auth:api'], function (){
    Route::get('anip-get-person', [ExternalServiceTestController::class, 'getPersonFromAnip'])->name('getPersonFromAnip');
 });

Route::group(['middleware' => 'auth:api'], function (){
   Route::resource('staff', StaffController::class);
   Route::resource("roles", RoleController::class);
   Route::get('current-user', [AuthController::class, 'currentUser'])->name('current-user');
   Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index');
   Route::put('update-password', [AuthController::class, 'updatePassword'])->name('password.update');
   Route::put('change-space', [AuthController::class, 'changeSpace'])->name('change.space');
   Route::put('update-staff-center', [StaffController::class, 'updateStaffCenter'])->name('update.staff.center');
   Route::put('update-staff-organizations', [StaffController::class, 'updateStaffOrganization'])->name('update.staff.organization');
   Route::put('update-profile/{profile}', [ProfileController::class, 'update'])->name('update.profile');
   Route::get('user-details/{npi}', [UserController::class, 'show'])->name('user-details');

});

/**
 * Register
 */

Route::post('register/init', [RegisterController::class, 'initRegistration'])->name('register.init');
Route::post('register/resend-otp', [RegisterController::class, 'resendOtp'])->name('register.resend-otp');
Route::post('register/check-otp', [RegisterController::class, 'checkOtp'])->name('register.check-otp');
Route::get('register/space-documents', [RegisterController::class, 'spaceDocuments'])->name('register.space-documents');
Route::post('register/store', [RegisterController::class, 'store'])->name('register.store');

Route::post('login/send-otp', [LoginController::class, 'sendOtp'])->name('login.send-otp');
Route::post('login/resend-otp', [LoginController::class, 'resendOtp'])->name('login.resend-otp');
Route::post('login', [LoginController::class, 'checkOtp'])->name('login');

Route::get('registration/search/states', [RegistrationSearchController::class, 'states'])->name('registration-search.states');
Route::get('registration/search/towns', [RegistrationSearchController::class, 'towns'])->name('registration-search.towns');
Route::get('registration/search/districts', [RegistrationSearchController::class, 'districts'])->name('registration-search.districts');
Route::get('registration/search/villages', [RegistrationSearchController::class, 'villages'])->name('registration-search.villages');
