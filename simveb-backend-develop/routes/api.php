<?php

use App\Http\Actions\Demand\AddDemandToCartAction;
use App\Http\Actions\SubmitPlateTransformationAction;
use App\Http\Actions\SubmitPrestigeLabelImmatriculationAction;
use App\Http\Actions\ToggleServiceAction;
use App\Http\Controllers\AccreditationController;
use App\Http\Controllers\ActionController;
use App\Http\Controllers\Admin\Config\BlacklistPersonController;
use App\Http\Controllers\Admin\Config\BorderController;
use App\Http\Controllers\Admin\Config\DistrictController;
use App\Http\Controllers\Admin\Config\GeographicalAreaController;
use App\Http\Controllers\Admin\Config\ImmatriculationTypeController;
use App\Http\Controllers\Admin\Config\IntlVehicleRegDocController;
use App\Http\Controllers\Admin\Config\LegalStatusController;
use App\Http\Controllers\Admin\Config\ManagementCenterController;
use App\Http\Controllers\Admin\Config\ManagementCenterTypeController;
use App\Http\Controllers\Admin\Config\NotificationConfigController;
use App\Http\Controllers\Admin\Config\OrganizationController;
use App\Http\Controllers\Admin\Config\OwnerTypeController;
use App\Http\Controllers\Admin\Config\ParkController;
use App\Http\Controllers\Admin\Config\PaymentProviderController;
use App\Http\Controllers\Admin\Config\PlateColorController;
use App\Http\Controllers\Admin\Config\PlateController;
use App\Http\Controllers\Admin\Config\PlateShapeController;
use App\Http\Controllers\Admin\Config\PriceController;
use App\Http\Controllers\Admin\Config\ReimmatriculationReasonController;
use App\Http\Controllers\Admin\Config\ReservedPlateNumberController;
use App\Http\Controllers\Admin\Config\ServiceController;
use App\Http\Controllers\Admin\Config\ServiceTypeController;
use App\Http\Controllers\Admin\Config\TitleReasonController;
use App\Http\Controllers\Admin\Config\TownController;
use App\Http\Controllers\Admin\Config\TransformationTypeController;
use App\Http\Controllers\Admin\Config\VillageController;
use App\Http\Controllers\Admin\Config\ZonageExcelController;
use App\Http\Controllers\Admin\Config\ZoneController;
use App\Http\Controllers\Admin\Demand\AdminOrderController;
use App\Http\Controllers\Admin\Demand\DemandController;
use App\Http\Controllers\Admin\Demand\DemandStatsController;
use App\Http\Controllers\Admin\Demand\DemandUpdatesHistoryController;
use App\Http\Controllers\Admin\Demand\TransactionStatsController;
use App\Http\Controllers\Admin\Service\ServiceStatsController;
use App\Http\Controllers\Admin\Treatment\PlateTransformationController;
use App\Http\Controllers\Admin\Vehicle\VehicleAdministrationController;
use App\Http\Controllers\PledgeLiftController;
use App\Http\Controllers\Space\SpaceController;
use App\Http\Controllers\Space\SpaceRegistrationRequestController;
use App\Http\Controllers\Alert\AlertTypeController;
use App\Http\Controllers\Auth\InvitationController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Auth\ProfileSearchController;
use App\Http\Controllers\Auth\ProfileStatsController;
use App\Http\Controllers\Auth\ProfileTypeController;
use App\Http\Controllers\BlacklistVehicleController;
use App\Http\Controllers\Client\Order\CommissionController;
use App\Http\Controllers\Client\Order\InvoiceController;
use App\Http\Controllers\Client\Order\PlateOrderController;
use App\Http\Controllers\Client\Order\PrintOrderController;
use App\Http\Controllers\ConfirmBlacklistVehicleController;
use App\Http\Controllers\DeclarantController;
use App\Http\Controllers\Identity\CompanyController;
use App\Http\Controllers\Identity\IdentityController;
use App\Http\Controllers\ImpressionDemandController;
use App\Http\Controllers\Institution\InstitutionController;
use App\Http\Controllers\Institution\InstitutionTypeController;
use App\Http\Controllers\NumberTemplateController;
use App\Http\Controllers\PledgeController;
use App\Http\Controllers\PlateStatsController;
use App\Http\Controllers\PoliceOfficerAssignmentController;
use App\Http\Controllers\Space\SpaceSuspensionLiftingRequestController;
use App\Http\Controllers\Space\SpaceSuspensionRequestController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\TestNotificationController;
use App\Http\Controllers\TitleReasonTypeController;
use App\Http\Controllers\Vehicle\VehicleAlertController;
use App\Http\Controllers\Vehicle\VehicleBrandController;
use App\Http\Controllers\Vehicle\VehicleCategoryController;
use App\Http\Controllers\Vehicle\VehicleCharacteristicCategoryController;
use App\Http\Controllers\Vehicle\VehicleCharacteristicController;
use App\Http\Controllers\Vehicle\VehicleEnergySourceController;
use App\Http\Controllers\Vehicle\VehicleOwnerController;
use App\Http\Controllers\Vehicle\VehiclePassageController;
use App\Http\Controllers\Vehicle\VehiclePassageHistoryController;
use App\Http\Controllers\Vehicle\VehiclePowerController;
use App\Http\Controllers\Vehicle\VehicleTypeController;
use App\Http\Controllers\VehicleTitle\ImmatriculationLabelController;
use App\Http\Controllers\VehicleTitle\PrestigeLabelImmatriculationController;
use App\Http\Controllers\VehicleTitle\TintedWindowsAuthorizationController;
use App\Http\Controllers\VehicleTitle\VehicleAdministrativeStatusController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\OppositionController;
use App\Models\SimvebFile;
use Illuminate\Support\Facades\Route;
use Ntech\UserPackage\Http\Controllers\Auth\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('immatriculation-demands/create', [DemandController::class, 'create'])->name('immatriculation-demands.create');
Route::post('immatriculation-demands', [DemandController::class, 'store'])->name('immatriculation-demands.store');

Route::group(['middleware' => ['auth:api', 'space.access']], function () {

    Route::get('wallets/transactions', [WalletController::class, 'transactions'])->name('wallets.transactions');
    Route::get('wallets/details', [WalletController::class, 'show'])->name('wallets.details');
    Route::post('wallets/recharge', [WalletController::class, 'recharge'])->name('wallets.recharge');

    Route::post('invitations/{invitation}/resend', [InvitationController::class, 'resend'])->name('invitations.resend');
    Route::resource('invitations', InvitationController::class)->only(['index', 'show', 'store', 'create']);
    Route::put('invitations/{invitation}/validate', [InvitationController::class, 'validateInvitation'])->name('invitations.validate');
    Route::put('invitations/{invitation}/deny', [InvitationController::class, 'deny'])->name('invitations.deny');

    Route::resource('organizations', OrganizationController::class);
    Route::apiResource('plate-colors', PlateColorController::class);
    Route::get('vehicle-characteristic-categories/characteristic-fields', [VehicleCharacteristicCategoryController::class, 'fetchCharacteristicFields'])->name('vehicle-characteristic-categories.characteristic-fields');
    Route::put('vehicle-characteristic-categories/update-field-names', [VehicleCharacteristicCategoryController::class, 'updateFieldNames'])->name('vehicle-characteristic-categories.update-field-names');
    Route::resource('vehicle-characteristic-categories', VehicleCharacteristicCategoryController::class)->except(['edit']);
    Route::resource('vehicle-characteristics', VehicleCharacteristicController::class)->except(['edit']);
    Route::apiResource('vehicle-types', VehicleTypeController::class);
    Route::apiResource('vehicle-categories', VehicleCategoryController::class);
    Route::resource('owner-types', OwnerTypeController::class)->except(['create']);

    Route::resource('space-registration-requests', SpaceRegistrationRequestController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('space-registration-requests/validate/{space_registration_request}', [SpaceRegistrationRequestController::class, 'validateRegistration'])->name('space-registration-requests.validate');
    Route::post('space-registration-requests/reject/{space_registration_request}', [SpaceRegistrationRequestController::class, 'rejectRegistration'])->name('space-registration-requests.reject');
    Route::get('spaces/members', [SpaceController::class, 'members'])->name('space.members');
    Route::get('spaces/details', [SpaceController::class, 'details'])->name('space.details');
    Route::resource('spaces', SpaceController::class)->except(['create', 'store']);
    Route::put('space-suspension-requests/{space_suspension_request}/validate-or-reject', [SpaceSuspensionRequestController::class, 'validateOrReject'])->name('space-suspension-requests.validate-or-reject');
    Route::resource('space-suspension-requests', SpaceSuspensionRequestController::class)->only(['index', 'store', 'show', 'update', 'create', 'edit']);
    Route::put('space-suspension-lifting-requests/{space_suspension_lifting_request}/validate-or-reject', [SpaceSuspensionLiftingRequestController::class, 'validateOrReject'])->name('space-suspension-lifting-requests.validate-or-reject');
    Route::resource('space-suspension-lifting-requests', SpaceSuspensionLiftingRequestController::class)->only(['index', 'store', 'show', 'update', 'create', 'edit']);

    Route::get('test-notification', [TestNotificationController::class, 'index'])->name('test.notifications');
    Route::apiResource('reserved-plate-numbers', ReservedPlateNumberController::class);
    Route::post('reserved-plate-numbers/validate-or-invalidate', [ReservedPlateNumberController::class, 'validateOrInvalidate'])->name('reserved-plate-numbers.validate-or-invalidate');
    Route::get('black-persons/file-format', [BlacklistPersonController::class, 'fileFormat'])->name('blacklist-persons.file-format');
    Route::post('black-persons/import', [BlacklistPersonController::class, 'import'])->name('blacklist-persons.import');
    Route::apiResource('blacklist-persons', BlacklistPersonController::class);
    Route::resource('commissions', CommissionController::class);
    Route::apiResource('vehicle-energy-sources', VehicleEnergySourceController::class);
    Route::resource('notification-configs', NotificationConfigController::class, ['only' => ['index', 'show', 'update']]);
    Route::apiResource('vehicle-powers', VehiclePowerController::class);
    Route::apiResource('vehicle-brands', VehicleBrandController::class);
    Route::apiResource('plate-shapes', PlateShapeController::class);
    Route::apiResource('service-types', ServiceTypeController::class);
    Route::resource('services', ServiceController::class);
    Route::apiResource('geographical-areas', GeographicalAreaController::class);
    Route::get('geographical-areas-staff/{id}', [GeographicalAreaController::class, 'getStaff']);
    Route::resource('reimmatriculation-reasons', ReimmatriculationReasonController::class)->only(['index', 'show', 'update']);
    Route::resource('actions', ActionController::class)->except(['edit', 'create']);
    Route::get('action/create-by-services/{service_id}', [ActionController::class, 'createByService'])->name('action.create-by-services');

    @include 'immatriculation-routes.php';
    @include 'vehicle-routes.php';
    @include 'treatments-routes.php';

    Route::apiResource('legal-statuses', LegalStatusController::class);
    Route::resource('towns', TownController::class)->except(['edit']);
    Route::resource('districts', DistrictController::class)->except(['edit']);
    Route::resource('villages', VillageController::class)->except(['edit']);
    Route::resource('zones', ZoneController::class);
    Route::get('export/{modelName}', [ZonageExcelController::class, 'export']);
    Route::post('import/{modelName}', [ZonageExcelController::class, 'import']);

    Route::get('get-districts-for-town', [TownController::class, 'getDistrictsForTown']);
    Route::get('get-villages-for-district', [DistrictController::class, 'getVillagesForDistrict']);
    Route::apiResource('tinted-windows-authorizations', TintedWindowsAuthorizationController::class);
    Route::get('tinted-windows-authorizations-expiry-status/{id}', [TintedWindowsAuthorizationController::class, 'getExpiryStatus']);
    Route::put('tinted-windows-authorizations-expiry-status/{id}', [TintedWindowsAuthorizationController::class, 'updateExpiryStatus']);
    Route::apiResource('intl-vehicle-reg-docs', IntlVehicleRegDocController::class);
    Route::get('intl-vehicle-reg-docs-expiry-status/{id}', [IntlVehicleRegDocController::class, 'getExpiryStatus']);
    Route::put('intl-vehicle-reg-docs-expiry-status/{id}', [IntlVehicleRegDocController::class, 'updateExpiryStatus']);

    Route::post('impression-demands/init', [ImpressionDemandController::class, 'initDemand'])->name('impression-demands.init');
    Route::post('impression-demands/reject', [ImpressionDemandController::class, 'rejectDemand'])->name('impression-demands.reject');
    Route::get('impression-demands/validation-create/{impression_demand}', [ImpressionDemandController::class, 'validationCreate'])->name('impression-demands.validation-create');
    Route::post('impression-demands/validate', [ImpressionDemandController::class, 'validateDemand'])->name('impression-demands.validate');
    Route::post('impression-demands/confirm', [ImpressionDemandController::class, 'confirmDemand'])->name('impression-demands.confirm');
    Route::post('impression-demands/confirm-reception/{impression_demand}', [ImpressionDemandController::class, 'confirmPlateReception'])->name('impression-demands.confirm-reception');
    Route::apiResource('impression-demands', ImpressionDemandController::class)->except(['update', 'destroy']);

    Route::get('plate-orders/confirmation-file-format', [PlateOrderController::class, 'confirmationFileFormat'])->name('plate-orders.confirmation-file-format');
    Route::post('plate-orders/generate-invoice-file/{invoice}', [PlateOrderController::class, 'generateInvoiceFile'])->name('plate-orders.generate-invoice-file');
    Route::get('plate-orders/invoices', [PlateOrderController::class, 'invoices'])->name('plate-orders.invoices');
    Route::get('plate-orders/invoices', [PlateOrderController::class, 'invoices'])->name('plate-orders.invoices');
    Route::post('plate-orders/confirm', [PlateOrderController::class, 'confirmOrder'])->name('plate-orders.confirm');
    Route::post('plate-orders/reject', [PlateOrderController::class, 'rejectOrder'])->name('plate-orders.reject');
    Route::post('plate-orders/pay', [PlateOrderController::class, 'payOrder'])->name('plate-orders.pay');
    Route::post('plate-orders/validate', [PlateOrderController::class, 'validateOrder'])->name('plate-orders.validate');
    Route::get('plate-orders/requests', [PlateOrderController::class, 'orderRequests'])->name('plate-orders.requests');
    Route::resource('plate-orders', PlateOrderController::class)->except(['update', 'destroy', 'edit']);

    Route::get('plates/stats', [PlateController::class, 'stats'])->name('plates.stats');
    Route::resource('plates', PlateController::class)->except(['edit', 'destroy']);

    Route::get('stats/imm', [StatsController::class, 'stats'])->name('immm.stats');
    Route::get('dashboard-stats', [StatsController::class, 'index'])->name('stats.index');

    Route::resource('parks', ParkController::class);
    Route::resource('borders', BorderController::class);
    Route::resource('management-center-types', ManagementCenterTypeController::class);
    Route::resource('management-centers', ManagementCenterController::class);

    Route::resource('institutions', InstitutionController::class)->except(['edit']);
    Route::apiResource('institution-types', InstitutionTypeController::class);

    Route::resource('declarants', DeclarantController::class);

    Route::resource('vehicle-administrative-status', VehicleAdministrativeStatusController::class);

    Route::resource('prestige-label-immatriculations', PrestigeLabelImmatriculationController::class);
    Route::post('submit-prestige-label-immatriculations', SubmitPrestigeLabelImmatriculationAction::class);

    Route::get('immatriculation-label', [ImmatriculationLabelController::class]);
    Route::resource('plate-transformations', PlateTransformationController::class);
    Route::post('submit-plate-transformations', SubmitPlateTransformationAction::class);
    //prices
    Route::resource('prices', PriceController::class);

    Route::apiResource('number-templates', NumberTemplateController::class);
    Route::resource('title-reasons', TitleReasonController::class)->except(['edit']);
    // ALERT TYPE
    Route::apiResource('alert-types', AlertTypeController::class);
    // IMMATRICULATION TYPES
    Route::resource('immatriculation-types', ImmatriculationTypeController::class)->except('edit');
    // VEHICLE'S PASSAGES AND ALERTS
    Route::post('vehicle-passages/get-vehicle-infos', [VehiclePassageController::class, 'vehicleInfos'])->name('vehicle-passage.get-vehicle-infos');
    Route::get('vehicle-passages/vehicle-history/{immatriculation_number}', [VehiclePassageHistoryController::class, 'history'])->name('vehicle-passage.get-vehicle-history');
    Route::get('vehicle-passages/passage-history/{vehicle_passage}', [VehiclePassageHistoryController::class, 'passageHistory'])->name('vehicle-passage.get-passage-history');
    Route::resource('vehicle-passages', VehiclePassageController::class);
    Route::resource('vehicle-alerts', VehicleAlertController::class);
    Route::get('black-vehicles/file-format', [BlacklistVehicleController::class, 'fileFormat'])->name('blacklist-vehicles.file-format');
    Route::post('black-vehicles/import', [BlacklistVehicleController::class, 'import'])->name('blacklist-vehicles.import');
    Route::put('blacklist-vehicles/{blacklist_vehicle}/validate', [ConfirmBlacklistVehicleController::class, 'confirm'])->name('blacklist-vehicle.validate');
    Route::put('blacklist-vehicles/{blacklist_vehicle}/reject', [ConfirmBlacklistVehicleController::class, 'reject'])->name('blacklist-vehicle.reject');
    Route::apiResource('blacklist-vehicles', BlacklistVehicleController::class)->except('update');
    // POLICE OFFICER ASSIGNMENT
    Route::post('police-officers/assignments/validate', [PoliceOfficerAssignmentController::class, 'assign'])->name('officer.assignment.validate');
    Route::post('police-officers/assignments/reject', [PoliceOfficerAssignmentController::class, 'reject'])->name('officer.assignment.reject');
    Route::resource('police-officers/assignments', PoliceOfficerAssignmentController::class)->names('officer.assignment')->except(['edit']);

    Route::get('profiles/search', ProfileSearchController::class)->name('profile.search');
    Route::get('profile/demands/{profile}', [ProfileSearchController::class, 'show'])->name('profile.demands');
    Route::resource('profiles', ProfileController::class)->only(['index', 'show']);

    //ACCREDITATIONS DEMAND
    Route::get('accreditations/user/search', [AccreditationController::class, 'userProfiles'])->name('accreditation.user.search');
    Route::post('accreditations/validate', [AccreditationController::class, 'accredit'])->name('accreditation.validate');
    Route::post('accreditations/reject', [AccreditationController::class, 'reject'])->name('accreditation.reject');
    Route::resource('accreditations', AccreditationController::class)->except('edit');

    // Identity
    Route::get('get-identity/{npi}', [IdentityController::class, 'show']);
    Route::get('get-identities/{npis}', [IdentityController::class, 'getIdentities']);
    Route::get('get-company/{ifu}', [CompanyController::class, 'show']);
    Route::put('profile-types/members/toggle-status', [ProfileTypeController::class, 'toggleMemberStatus'])->name('profile-types.members.toggle-status');
    Route::get('profile-types/members', [ProfileTypeController::class, 'getMembers'])->name('profile-types.members');
    Route::put('profile-types/{profile_type}/update-plate-colors', [ProfileTypeController::class, 'updatePlateColors'])->name('profile-types.update-plate-colors');
    Route::resource('profile-types', ProfileTypeController::class)->only(['index', 'show', 'create']);

    //Demands
    Route::get('my-pending-demands', [DemandController::class, 'myPendingDemands'])->name('my-pending-demands');
    Route::get('my-treated-demands', [DemandController::class, 'myTreatedDemands'])->name('my-treated-demands');
    Route::get('admin-demands', [DemandController::class, 'index'])->name('admin.demands.index');
    Route::get('interpol-demands', [DemandController::class, 'interpolDemands'])->name('interpol.demands');
    Route::get('demands/{demand}', [DemandController::class, 'show'])->name('demands.details');
    Route::get('interpol/demands/{demand}', [DemandController::class, 'showInterpoleDemand'])->name('interpol.demands.details');
    Route::put('demands/{demand}/validate-updates', [DemandController::class, 'validateUpdates'])->name('demand-updates.validate');

    Route::post('demand-updates/validate', [DemandUpdatesHistoryController::class, 'validateAll'])->name('demand-updates.historics.validate');

    // STATS ROUTES
    Route::get('stats/demands/total', [DemandStatsController::class, 'total'])->name('stats.demands-total');
    Route::get('stats/demands/total-by-vehicle-category', [DemandStatsController::class, 'totalByVehicleCategory'])->name('stats.demands-by-vehicle-category');
    Route::get('stats/demands/total-by-service', [DemandStatsController::class, 'totalByService'])->name('stats.demands-by-service');
    // Route::get('stats/demands/overdue', [DemandStatsController::class, 'overdue'])->name('stats.overdue-demands');

    Route::get('stats/orders', [AdminOrderController::class, 'stats'])->name('stats.orders');

    // Exports ROUTES
    Route::get('exports/excel/demands', [DemandController::class, 'excelExport'])->name('exports.demands-excel');
    Route::get('exports/pdf/demands', [DemandController::class, 'pdfExport'])->name('exports.demands-pdf');
    Route::get('exports/excel/orders', [AdminOrderController::class, 'excelExport'])->name('exports.orders-excel');
    Route::get('exports/pdf/orders', [AdminOrderController::class, 'pdfExport'])->name('exports.orders-pdf');

    Route::get('stats/transactions/total-amount', [TransactionStatsController::class, 'total'])->name('stats.transactions-total-amount');

    Route::get('stats/services/popular', [ServiceStatsController::class, 'popular'])->name('stats.popular-service');
    Route::get('stats/services/unpopular', [ServiceStatsController::class, 'unpopular'])->name('stats.unpopular-service');

    Route::get('stats/plates/total', [PlateStatsController::class, 'total'])->name('stats.plates-total');

    Route::get('stats/profiles/total-by-profile-types', [ProfileStatsController::class, 'totalByTypes'])->name('stats.profile-by-types');

    // Admin Orders
    Route::get('admin-orders/{order}', [AdminOrderController::class, 'show'])->name('admin-orders.details');
    Route::get('admin-orders', [AdminOrderController::class, 'index'])->name('admin-orders.index');
    Route::post('invoices/{order}/generate', [InvoiceController::class, 'generate'])->name('invoice.generate');

    Route::prefix('admin')->name('admin.')->group(function () {
        // BACKOFFICE VEHICLES ROUTES
        Route::get('vehicles', [VehicleAdministrationController::class, 'index'])->name('vehicle.list');
        Route::get('vehicles/vehicle-details', [VehicleAdministrationController::class, 'show'])->name('vehicle.details');
    });

    Route::get('affiliate/vehicles/vehicle-details', [VehicleAdministrationController::class, 'affiliateShow'])->name('affiliate.vehicle.details');

    // Activate or deactivate a service
    Route::put('toggle-service/{service}', ToggleServiceAction::class)->name('toggle.service');

    // PLEDGE
    Route::resource('pledge', PledgeController::class)->except('edit');
    Route::get('pledge/vehicle/owner', [PledgeController::class, 'showVehicleAndOwnerByVin']);
    Route::get('pledge/clerk/court', [PledgeController::class, 'getClerkByCourt']);
    Route::put('pledge/affectation/{pledge}', [PledgeController::class, 'affectationToClerk'])->name('pledge.affectationToClerk');
    Route::put('pledge/validate/{pledge}', [PledgeController::class, 'validatePledge']);
    Route::put('pledge/reject/{pledge}', [PledgeController::class, 'reject'])->name('pledge.reject');
    Route::put('pledge/lift/{pledge}', [PledgeController::class, 'liftPledge'])->name('pledge.liftPledge');
    Route::get('pledge/liftable/list', [PledgeController::class, 'listLiftablePledges'])->name('pledges.liftable');

    // LIFT PLEDGE
    Route::resource('pledge-lift', PledgeLiftController::class)->only(['index', 'show', 'update', 'destroy']);
    Route::put('pledge-lift/reject/{pledgeLift}', [PledgeLiftController::class, 'rejectLift'])->name('pledge-lift.reject');
    Route::put('pledge-lift/validate/{pledgeLift}', [PledgeLiftController::class, 'validateLift'])->name('pledge-lift.validate');

    //PLEDGE STATS
    Route::get('pledge-stats/total-pledges', [PledgeController::class, 'totalPledges']);
    Route::get('pledge-stats/active-closed-pledges', [PledgeController::class, 'activeClosePledges']);


    // OPPOSITION
    Route::resource('oppositions', OppositionController::class)->except('edit');
    Route::get('owner/vehicles', [OppositionController::class, 'showVehiclesByNpiOrIfu']);
    Route::put('opposition/validate/{opposition}', [OppositionController::class, 'validateOpposition']);
    Route::put('opposition/reject/{opposition}', [OppositionController::class, 'reject'])->name('opposition.reject');
    Route::put('opposition/lift/{opposition}', [OppositionController::class, 'lift'])->name('opposition.lift');


    // OPPOSITION STATS
    Route::get('opposition-stats/total', [OppositionController::class, 'oppositionTotal']);
    Route::get('opposition-stats/active-closed-opposition', [OppositionController::class, 'activeCloseOpposition']);


    Route::get('print-orders/search', [PrintOrderController::class, 'search'])->name('print-orders.search');
    Route::post('print-orders/confirm-affectation', [PrintOrderController::class, 'confirmAffectation'])->name('print-orders.confirm-affectation');
    Route::post('print-orders/print-plate', [PrintOrderController::class, 'printPlate'])->name('print-orders.print-plate');
    Route::post('print-orders/print-gray-card', [PrintOrderController::class, 'printGrayCard'])->name('print-orders.print-gray-card');
    Route::post('print-orders/validate-or-reject', [PrintOrderController::class, 'validateOrRejectPrint'])->name('print-orders.validate-or-reject');
    Route::resource('print-orders', PrintOrderController::class)->only(['index', 'show']);

    // TITLE REASON TYPE
    Route::resource('title-reason-types',TitleReasonTypeController::class)->except(['edit']);

    // TRANSFORMATION TYPE
    Route::apiResource('transformation-types',TransformationTypeController::class);

    //Payment Providers
    Route::get('payment-providers/active', [PaymentProviderController::class, 'getActive'])->name('payment-providers.getActive');
    Route::put('payment-providers/{payment_provider}/toggle', [PaymentProviderController::class, 'toggle'])->name('payment-providers.toggle');
    Route::put('payment-providers/{payment_provider}/default', [PaymentProviderController::class, 'default'])->name('payment-providers.default');
    Route::resource('payment-providers', PaymentProviderController::class)->only(['index', 'show', 'update']);

    @include 'spaces.php';
});

Route::post('vehicle-owners-info', [VehicleOwnerController::class, 'getOwnerInfo'])->name('vehicle-owners.infos');
Route::post('vehicle-owners-subscribe', [VehicleOwnerController::class, 'subscribe'])->name('vehicle-owners.subscribe');
Route::get('verify-email', [AuthController::class, 'verifyEmail'])->name('email.verify');
Route::get('vehicle-owner-demands/{vehicle_owner}', [DemandController::class, 'getDemandsByOwner'])->name('owner.demands');

//demands
Route::post('add-demand-to-cart/{demand}', AddDemandToCartAction::class)->name('demand.submit');

@include 'portal-routes.php';
@include 'clients-routes.php';

Route::get('/download/{id}', function ($id) {
    $file = SimvebFile::findOrFail($id);
    $path =  public_path() . '/' .  str_replace('public', 'storage', $file->path['path']);
    return response()->download($path, $file->name, ['Content-Type: application/octet-stream']);
})->name('download');

