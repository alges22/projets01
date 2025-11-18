<?php

use App\Consts\AvailableServiceType;
use App\Consts\Utils;
use App\Enums\ImmatriculationTypeEnum;
use App\Enums\PrintOrderTypesEnum;
use App\Enums\ProfileTypesEnum;
use App\Enums\Status;
use App\Exceptions\UnknownServiceException;
use App\Http\Resources\ProfileResource;
use App\Models\Auth\Profile;
use App\Models\Config\BlacklistPerson;
use App\Models\Config\ImmatriculationType;
use App\Models\Config\Organization;
use App\Models\Config\Service;
use App\Models\Config\TreatmentTime;
use App\Models\GrayCard;
use App\Models\Immatriculation\Immatriculation;
use App\Models\Immatriculation\LastImmatriculation;
use App\Models\Mutation;
use App\Models\Order\Cart;
use App\Models\Order\Demand;
use App\Models\PlateTransformation;
use App\Models\Reimmatriculation;
use App\Models\SaleDeclaration;
use App\Models\Title\TitleDeposit;
use App\Models\Title\TitleRecovery;
use App\Models\Vehicle\GmaVehicle;
use App\Models\Vehicle\GmdVehicle;
use App\Models\Vehicle\GovVehicle;
use App\Models\Vehicle\Vehicle;
use App\Models\VehicleTransformation;
use App\Notifications\NotificationSender;
use App\Repositories\Crud\CrudRepository;
use App\Services\Declaration\SaleDeclarationServiceAdapter;
use App\Services\Demand\DemandHandlerService;
use App\Services\Duplicate\GrayCardDuplicateServiceAdapter;
use App\Services\Duplicate\PlateDuplicateServiceAdapter;
use App\Services\GlassEngraving\GlassEngravingServiceAdapter;
use App\Services\Immatriculation\ImmatriculationLabelServiceAdapter;
use App\Services\Immatriculation\ImmatriculationServiceAdapter;
use App\Services\Mutation\MutationServiceAdapter;
use App\Services\PlateTransformation\PlateTransformationServiceAdapter;
use App\Services\Reimmatriculation\ReimmatriculationServiceAdapter;
use App\Services\SmsService;
use App\Services\TintedWindow\TintedWindowAuthorizationServiceAdapter;
use App\Services\Title\TitleDepositServiceAdapter;
use App\Services\Title\TitleRecoveryServiceAdapter;
use App\Services\VehicleTransformation\VehicleTransformationServiceAdapter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Ntech\MetadataPackage\Models\MetaData;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Database\Eloquent\Builder;

if (!function_exists('generateReference')) {
    function generateReference($prefix = "IM", $name = null): string
    {
        $pref = "";
        if ($name) {
            $nameArr = explode('_', $name);
            foreach ($nameArr as $value) {
                $pref .= $value[0];
            }
        } else {
            $pref = $prefix;
        }

        return $pref . "-" . Carbon::now()->timestamp;
    }
}

function getMetaDataGroups()
{
    return MetaData::pluck('data')->toArray();
}

if (!function_exists('getMetaValue')) {
    function getMetaValue(string $key)
    {
        $metaGroups = getMetaDataGroups();

        $metaArray = [];
        foreach ($metaGroups as $metaGroup) {
            foreach ($metaGroup as $meta) {
                array_push($metaArray, $meta);
            }
        }

        $value = optional(collect($metaArray)->where('key', $key)->first())['value'];

        if (is_null($value)) {
            return $value;
        }
        if (is_numeric($value)) {
            return (int)$value;
        }
        if (json_decode($value)) {
            return json_decode($value);
        }

        return $value;
    }
}

function shouldBlackList(array $criteria): bool
{
    return BlacklistPerson::query()->where($criteria)->first() != null;
}

function getStaffToAssignDemand($organizationId)
{
    //TODO check role
    return Profile::query()
        ->withCount('activeTreatments')
        ->whereHas('identity.staff.organizations', function ($query) use ($organizationId) {
            $query->where('id', $organizationId);
        })
        ->orderBy('active_treatments_count')
        ->first();
}

function getInterpolStaffToAssignDemand()
{
    return Profile::query()
        ->whereHas('type', function ($query) {
            $query->where('code', ProfileTypesEnum::interpol->name);
        })
        ->inRandomOrder()
        ->first();
}

function getInterpolService()
{
    return Organization::query()->where('is_interpol', true)->first();
}

function serviceExist($serviceId)
{
    return Organization::query()->find($serviceId)->exists('id', true);
}

if (!function_exists('updateOrRememberCache')) {
    function updateOrRememberCache($key, $ttl, mixed $data)
    {
        if (Cache::get($key)) {
            Cache::forget($key);
        }

        Cache::remember($key, $ttl, function () use ($data) {
            return $data;
        });
    }
}

if (!function_exists('hideEmailAddress')) {
    function hideEmailAddress($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            list($first, $last) = explode('@', $email);
            $first = str_replace(substr($first, '3'), str_repeat('*', strlen($first) - 3), $first);
            $last = explode('.', $last);
            $last_domain = str_replace(substr($last['0'], '1'), str_repeat('*', strlen($last['0']) - 1), $last['0']);
            $hideEmailAddress = $first . '@' . $last_domain . '.' . $last['1'];
            return $hideEmailAddress;
        }
    }
}

if (!function_exists('maskTelephone')) {
    function maskTelephone($telephone)
    {
        return Str::mask($telephone, '*', 3, -2);
    }
}

function getOnlineProfile(): Profile|Model|null
{
    return Profile::query()->with(['type'])->find(auth()->user()?->online_profile_id);
}

if (!function_exists('getCart')) {
    function getCart(): Model|null
    {
        $onlineProfile = getOnlineProfile();
        if ($onlineProfile) {
            $repository = new CrudRepository(Cart::class);
            if ($onlineProfile->type?->code == ProfileTypesEnum::user->name) {
                $cart = $repository->findWhere([
                    'profile_id' => $onlineProfile->id
                ]);
                if (!$cart) {
                    $cart = $repository->store([
                        'profile_id' => $onlineProfile->id
                    ]);
                }
            } else {
                $cart = $repository->findWhere([
                    'institution_id' => $onlineProfile->institution_id
                ]);
                if (!$cart) {
                    $cart = $repository->store([
                        'institution_id' => $onlineProfile->institution_id
                    ]);
                }
            }
            $cart->load([
                'demands:id,reference,model_type,model_id,service_id' => [
                    'service:id,name,type_id' =>
                    [
                        'type:id,code',
                    ]
                ],
            ]);

            return $cart;
        }
        return null;
    }
}
if (!function_exists('emptyCart')) {
    function emptyCart($resetDemands = false): Model
    {
        DB::beginTransaction();
        try {
            $cart = getCart();

            $cart->update(['amount' => 0]);
            if ($resetDemands) {
                $cart->demands()->update(['status' => Status::pending->name]);
            }
            $cart->demands()->detach();

            $cart->refresh();

            DB::commit();

            return $cart;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::debug($e);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, __('errors.server_error'));
        }
    }
}

function removeDemandFromDemand(Demand $demand): Model
{
    DB::beginTransaction();
    try {
        $cart = getCart();

        $demandAmount = $cart->demands()->where('demand_id', $demand->id)->first()?->pivot?->amount ?? 0;

        $cart->demands()->detach($demand->id);
        $cart->update(['amount' => $cart->amount - $demandAmount]);
        $demand->update(['status' => Status::pending->name]);

        $cart->refresh();

        DB::commit();

        return $cart;
    } catch (\Exception $e) {
        DB::rollBack();
        Log::debug($e);
        abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, __('errors.server_error'));
    }
}




/**
 * @throws UnknownServiceException
 */
function getDemandCost(Demand $demand): float|int
{
    $service = $demand->service;
    $cost = 0;
    $priceVariation = 0;

    foreach ($service->vehicleOwnerTypes as $ownerType) {
        if ($ownerType?->pivot->model_id == $demand->vehicle->ownerType?->id) {
            $priceVariation += $ownerType->pivot->price;
        }
    }

    foreach ($service->vehicleCategories as $category) {
        if ($category?->pivot->model_id == $demand->vehicle->category?->id) {
            $priceVariation += $category->pivot->price;
        }
    }

    foreach ($demand->vehicle->characteristics as $vehicleCharacteristic) {
        foreach ($service->vehicleCharacteristics as $characteristic) {
            if ($characteristic?->pivot->model_id == $vehicleCharacteristic?->pivot->vehicle_characteristic_id) {
                $priceVariation += $characteristic->pivot->price;
            }
        }
    }
    $cost = $demand->service->cost + $priceVariation;

    return $cost;
}

/**
 * @throws UnknownServiceException
 */
function getDemandStep(Demand $demand): float|int
{
    $step = 1;
    switch ($demand->service->type->code) {
        case AvailableServiceType::IMMATRICULATION_STANDARD:
            break;
        case AvailableServiceType::IMMATRICULATION_PRESTIGE_NUMBER:
            break;
        case AvailableServiceType::IMMATRICULATION_PRESTIGE_LABEL:
            break;
        case AvailableServiceType::IMMATRICULATION_PRESTIGE_NUMBER_LABEL:
            if ($demand->model->plateColor) {
                $step = 2;
            }
            if ($demand->service->documents()->count() > 0) {
                if ($demand->files()->count() > 0) {
                    $step = 3;
                }
            } else {
                $step = 3;
            }
            if ($demand->status == Status::in_cart->name) {
                $step = 4;
            }
            break;
        case AvailableServiceType::SALE_DECLARATION:
            break;
        case AvailableServiceType::TITLE_RECOVERY:
        case AvailableServiceType::TITLE_DEPOSIT:
            break;
        case AvailableServiceType::MUTATION:
            break;
        case GrayCard::class:
            break;
        case AvailableServiceType::RE_IMMATRICULATION:
            break;
        case AvailableServiceType::PLATE_TRANSFORMATION:
            break;
        case AvailableServiceType::TINTED_WINDOW_AUTHORIZATION:
            break;
        case AvailableServiceType::GLASS_ENGRAVING:
            break;
        case AvailableServiceType::PLATE_DUPLICATE:
            break;
        case AvailableServiceType::GRAY_CARD_DUPLICATE:
            break;
        case AvailableServiceType::VEHICLE_TRANSFORMATION:
            break;
        default:
            throw new UnknownServiceException;
    }

    return $step;
}

if (!function_exists('getPrintOrderType')) {
    /**
     * @throws UnknownServiceException
     */
    function getPrintOrderType(Demand $demand): string
    {
        switch ($demand->service->type->code) {
            case AvailableServiceType::IMMATRICULATION_STANDARD:
            case AvailableServiceType::IMMATRICULATION_PRESTIGE_NUMBER:
            case AvailableServiceType::IMMATRICULATION_PRESTIGE_LABEL:
            case AvailableServiceType::IMMATRICULATION_PRESTIGE_NUMBER_LABEL:
            case AvailableServiceType::RE_IMMATRICULATION:
            case AvailableServiceType::PLATE_TRANSFORMATION:
                $orderType = PrintOrderTypesEnum::both->name;
                break;
            case AvailableServiceType::GRAY_CARD_DUPLICATE:
            case AvailableServiceType::VEHICLE_TRANSFORMATION:
                $orderType = PrintOrderTypesEnum::gray_card->name;
                break;
            case AvailableServiceType::PLATE_DUPLICATE:
                $orderType = PrintOrderTypesEnum::plate->name;
                break;
            case AvailableServiceType::TINTED_WINDOW_AUTHORIZATION:
            case AvailableServiceType::GLASS_ENGRAVING:
                $orderType = "";
                break;
            default:
                throw new UnknownServiceException;
        }

        return $orderType;
    }
}

function logAssigmentError(string $message, string $demandRef): void
{
    Log::error("******** Auto assignment ************ \n Error : $message. \n Demand : $demandRef");
}

if (!function_exists('amountFormat')) {
    function amountFormat($amount): string
    {
        return number_format($amount, 0, ',', ' ');
    }
}

function reserveNumber(string $number, Vehicle $vehicle)
{
    $immatriculationFormat = $vehicle->category->immatriculationFormat;

    //if (ReservedPlateNumber::query()->)
}

function getLastImmNumber($immatriculationFormat)
{
    return isset($immatriculationFormat) ?
        LastImmatriculation::query()
            ->where('vehicle_category_id', $immatriculationFormat?->vehicle_category_id)
            ->first() :
        LastImmatriculation::query()->first();
}

if (!function_exists('sortedArrayUnique')) {
    function sortedArrayUnique(array $array): array
    {
        $sortedArray = [];

        foreach (array_unique($array) as $value) {
            $sortedArray[] = $value;
        }

        return $sortedArray;
    }
}

function getDemandAdapter(Service $service): DemandHandlerService
{
    return match ($service->type->code) {
        AvailableServiceType::IMMATRICULATION_STANDARD,
        AvailableServiceType::IMMATRICULATION_PRESTIGE_NUMBER_LABEL,
        AvailableServiceType::IMMATRICULATION_PRESTIGE_NUMBER => DemandHandlerService::use(ImmatriculationServiceAdapter::class),
        AvailableServiceType::IMMATRICULATION_PRESTIGE_LABEL => DemandHandlerService::use(ImmatriculationLabelServiceAdapter::class),
        AvailableServiceType::SALE_DECLARATION => DemandHandlerService::use(SaleDeclarationServiceAdapter::class),
        AvailableServiceType::MUTATION => DemandHandlerService::use(MutationServiceAdapter::class),
        AvailableServiceType::TITLE_DEPOSIT => DemandHandlerService::use(TitleDepositServiceAdapter::class),
        AvailableServiceType::TITLE_RECOVERY => DemandHandlerService::use(TitleRecoveryServiceAdapter::class),
        AvailableServiceType::RE_IMMATRICULATION => DemandHandlerService::use(ReimmatriculationServiceAdapter::class),
        AvailableServiceType::PLATE_TRANSFORMATION => DemandHandlerService::use(PlateTransformationServiceAdapter::class),
        AvailableServiceType::TINTED_WINDOW_AUTHORIZATION => DemandHandlerService::use(TintedWindowAuthorizationServiceAdapter::class),
        AvailableServiceType::GLASS_ENGRAVING => DemandHandlerService::use(GlassEngravingServiceAdapter::class),
        AvailableServiceType::PLATE_DUPLICATE => DemandHandlerService::use(PlateDuplicateServiceAdapter::class),
        AvailableServiceType::GRAY_CARD_DUPLICATE => DemandHandlerService::use(GrayCardDuplicateServiceAdapter::class),
        AvailableServiceType::VEHICLE_TRANSFORMATION => DemandHandlerService::use(VehicleTransformationServiceAdapter::class),
        default => throw new UnknownServiceException()
    };
}

function getServiceProcessModel(Service $service): mixed
{
    return match ($service->type->code) {
        AvailableServiceType::IMMATRICULATION_STANDARD => Immatriculation::class,
        AvailableServiceType::IMMATRICULATION_PRESTIGE_NUMBER => Immatriculation::class,
        AvailableServiceType::IMMATRICULATION_PRESTIGE_LABEL => Immatriculation::class,
        AvailableServiceType::IMMATRICULATION_PRESTIGE_NUMBER_LABEL => Immatriculation::class,
        AvailableServiceType::SALE_DECLARATION => SaleDeclaration::class,
        AvailableServiceType::MUTATION => Mutation::class,
        AvailableServiceType::TITLE_DEPOSIT => TitleDeposit::class,
        AvailableServiceType::TITLE_RECOVERY => TitleRecovery::class,
        AvailableServiceType::RE_IMMATRICULATION => Reimmatriculation::class,
        AvailableServiceType::PLATE_TRANSFORMATION => PlateTransformation::class,
        AvailableServiceType::VEHICLE_TRANSFORMATION => VehicleTransformation::class,
        default => throw new UnknownServiceException()
    };
}

function getDemandSteps(Demand $demand)
{
    $treatment = $demand->activeTreatment;
    $date = null;
    $author = null;

    if ($treatment) {
        switch ($demand->status) {
            case Status::verified->name:
                $author = $treatment->verifiedBy;
                $date = $treatment->verified_at;
                break;
            case Status::validated->name:
                $author = $treatment->validatedBy;
                $date = $treatment->validated_at;
                break;
            case Status::pre_validated->name:
                $author = $treatment->preValidatedBy;
                $date = $treatment->pre_validated_at;
                break;
            case Status::rejected->name:
                $author = $treatment->rejectedBy;
                $date = $treatment->rejected_at;
                break;
            case Status::print_order_validated->name:
                $author = $treatment->printedBy;
                $date = $treatment->printed_at;
                break;
            case Status::assigned_to_center->name:
                $author = $treatment->assignedToCenterBy;
                $date = $treatment->assigned_to_center_at;
                break;
            case Status::assigned_to_staff->name:
                $author = $treatment->assignedToStaffBy;
                $date = $treatment->assigned_to_staff_at;
                break;
            case Status::assigned_to_service->name:
                $author = $treatment->assignedToServiceBy;
                $date = $treatment->assigned_to_service_at;
                break;
            default:
                break;
        }
    }

    return $demand->service->steps()
        ->select(['steps.id', 'status', 'label'])
        ->when(Str::contains(request()->path(), 'client'), fn($query) => $query->whereIn('status', [
            Status::submitted->name,
            Status::validated->name,
            Status::affected_to_interpol->name,
            Status::print_order_emitted->name,
            Status::closed->name,
        ]))
        ->get()
        ->map(function ($item) use ($demand, $date, $author, $treatment) {
            return [
                'id' => $item->id,
                'label' => $item->label,
                'status' => $item->status,
                'is_done' => TreatmentTime::query()
                    ->where('status', $item->status)
                    ->where('treatment_id', $treatment?->id)
                    ->whereNotNull('end_at')
                    ->exists(),
                'is_current' => $demand->status == $item->status,
                'author' => $author ? new ProfileResource($author) : null,
                'date' => empty($date) ? null : (is_string($date) ? $date : Carbon::parse($date)->translatedFormat(Utils::COMMON_DATE_FORMAT)),
            ];
        });
}

if (!function_exists('getUniqueProfileNumber')) {
    function getUniqueProfileNumber(): string
    {
        $number = mt_rand(0, 9999999999);
        $number = str_repeat('0', 10 - strlen($number)) . $number;

        if (Profile::where('number', $number)->exists()) {
            return getUniqueProfileNumber();
        }

        return $number;
    }
}

if (!function_exists('getVehicleByImmatriculation')) {
    function getVehicleByImmatriculation(string $immatriculationNumber)
    {
        $cleanedImmatriculation = preg_replace('/[\s\xA0]/u', '', $immatriculationNumber);

        $immatriculation = Immatriculation::whereRaw("REPLACE(REPLACE(REPLACE(number_label, CHR(160), ''), ' ', ''), '\t', '') = ?", [$cleanedImmatriculation])->first();

        return $immatriculation?->vehicle;
    }
}

if (!function_exists('getClassBasename')) {
    function getClassBasename(string $class)
    {
        $class = explode('\\', $class);
        return end($class);
    }
}

if (!function_exists('getPhoneNumberGsm')) {
    function getPhoneNumberGsm($phone_number)
    {
        $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();
        try {
            $phoneNumberProto = $phoneUtil->parse($phone_number, "BJ");
            $carrierMapper = \libphonenumber\PhoneNumberToCarrierMapper::getInstance();
            $mobile_gsm = strtolower($carrierMapper->getNameForNumber($phoneNumberProto, "en"));

            return $mobile_gsm;
        } catch (\libphonenumber\NumberParseException $e) {
            var_dump($e);
        }
    }
}

if (!function_exists('getVehicleImmatriculationType')) {
    function getVehicleImmatriculationType(string $vin): ImmatriculationType|Model
    {
        try {
            $response = Http::get(config('app.sandbox_host') . '/vehicles/' . $vin);
            if ($response->failed()) {
                throw new HttpException(ResponseAlias::HTTP_NOT_FOUND, "Véhicule Introuvable");
            } else {
                if (GovVehicle::where('vin', $vin)->first()) {
                    $immatriculationType = ImmatriculationType::where('code', ImmatriculationTypeEnum::gov->name)->first();
                } elseif (GmaVehicle::where('vin', $vin)->first()) {
                    $immatriculationType = ImmatriculationType::where('code', ImmatriculationTypeEnum::mai->name)->first();
                } elseif (GmdVehicle::where('vin', $vin)->first()) {
                    $immatriculationType = ImmatriculationType::where('code', ImmatriculationTypeEnum::diplomatic->name)->first();
                } else {
                    $immatriculationType = ImmatriculationType::where('code', ImmatriculationTypeEnum::common->name)->first();
                }

                return $immatriculationType->load('plateColors');
            }
        } catch (\Exception $exception) {
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }
}

function sendMail(string $email = null, Model $model = null, string $name, array $data)
{
    try {
        if ($email) {
            Notification::route('mail', $email)->notify(new NotificationSender($name, ['mail'], $data));
        } else if ($model) {
            Notification::send($model, new NotificationSender($name, data: $data));
        }
    } catch (\Exception $exception) {
        Log::debug($exception);
        abort(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY, $exception->getMessage());
    }
}

if (!function_exists('sendNotification')) {
    function sendNotification(string $notificationName, ?Model $notifiable, array $data = null, array $channels = ['mail'])
    {
        if ($notifiable) {
            Notification::send($notifiable, new NotificationSender($notificationName, channels: $channels, data: $data));
        }
    }
}

if (!function_exists('sendSms')) {
    function sendSms($telephone, $message)
    {
        (new SmsService)->send($telephone, $message);
    }
}

if (!function_exists('checkPhoneNumber')) {
    function checkPhoneNumber($phone_number, $country = '229'): string
    {
        $phone_number = str_replace(' ', '', $phone_number);
        $phone_number = str_replace('-', '', $phone_number);
        $phone_number = str_replace('+', '', $phone_number);

        if (strlen($phone_number) == 8) {
            return $country . $phone_number;
        }

        if (strlen($phone_number) > 8 && substr($phone_number, 0, strlen($country)) == $country) {
            return $phone_number;
        }

        return $phone_number;
    }
}

if (!function_exists('getModelAttributeLabelFromEnum')) {
    function getModelAttributeLabelFromEnum(Model $model, string $attribute, $enum)
    {
        if (!empty($model->$attribute)) {
            $reflection = new ReflectionEnumBackedCase($enum, $model->$attribute);

            return $reflection->getValue();
        }
        return '';
    }
}
