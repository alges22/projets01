<?php
use App\Consts\AvailableServiceType;
use App\Consts\Roles;
use App\Enums\ProfileTypesEnum;
use App\Enums\Status;
use App\Models\Auth\Profile;
use App\Models\Config\Service;
use App\Models\Order\Cart;
use App\Models\Order\Demand;
use App\Models\Order\Order;
use App\Models\Plate\PlateColor;
use App\Models\Plate\PlateShape;
use App\Services\FedapayService;
use FedaPay\Transaction;
use Illuminate\Http\UploadedFile;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Support\Facades\Storage;
use function Pest\Laravel\actingAs;
use FedaPay\FedaPay;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    DB::beginTransaction();
    $service = Service::where('code', AvailableServiceType::IMMATRICULATION_STANDARD)->first();


    FedaPay::setApiKey(config('fedapay.secret_key'));
    FedaPay::setEnvironment(config('fedapay.environment'));


    $this->service = $service;
    $this->vins = ['1FMCU0G99GUC12345', '5J7RE48739L123456', 'ZAR95200002123456', '1GNEK13T9XJ550095', 'ZARBA541100023456'];
    $this->fedapayService = new FedapayService();
    $this->paymentReference = 'XXXXXX';
});

afterEach(function() {
    DB::commit();
});

test('User can access the list of services', function () {

    actingAsProfile(ProfileTypesEnum::user->name)
        ->get('/api/client/services')
        ->assertStatus(200);
});

test('User can access the list of immatriculation service childreen', function () {

    actingAsProfile(ProfileTypesEnum::user->name)
        ->get('/api/client/services/IMMATRICULATION')
        ->assertStatus(200);
});


test('User can access immatriculation demand create route', function () {

    actingAsProfile(ProfileTypesEnum::user->name)
        ->get("/api/client/demands/create/{$this->service->id}")
        ->assertStatus(200);
});


test('User gets all required data to make a demand', function () {
    actingAsProfile(ProfileTypesEnum::user->name)
        ->get("/api/client/demands/create/{$this->service->id}")
        ->assertJson(fn (AssertableJson $json) =>
            $json->hasAll(['required_documents', 'plate_colors', 'plate_shapes'])
                ->etc()
        );
});

test('Check that required documents are sent to the user', function () {

    actingAsProfile(ProfileTypesEnum::user->name)
        ->get("/api/client/demands/create/{$this->service->id}")
        ->assertJsonFragment([
            'required_documents' => $this->service->documents()->select(['id', 'description'])->get()->toArray()
        ]);
});


test('User can make a standard immatriculation demand and uploaded files are stored in the disk', function () {

    $vin = Arr::random($this->vins);
    $plateColor = PlateColor::inRandomOrder()->first();
    $frontPlateShape = PlateShape::inRandomOrder()->first();
    $backPlateShape = PlateShape::inRandomOrder()->first();

    foreach ($this->service->documents()->select('id')->get() as $document)
    {
        $files[] = [
            'type_id' => $document->id,
            'file' => UploadedFile::fake()->image('exampleimage.jpg')
        ];
    }


    $user = getUserWithProfile(ProfileTypesEnum::user->name);
    $numberOfOrdersBeforeApiCall = Order::where('profile_id', $user['online_profile_id'])->count();
    Log::debug('NPITEST', [$user->identity->npi]);
    $response = actingAs($user)
        ->postJson('api/client/add-demand-to-cart', [
            'npi' => $user->identity->npi,
            'vin' => $vin,
            'service_id' => $this->service->id,
            'desired_number' => fake()->randomNumber(4, true),
            'label' => Str::random(8),
            'plate_color_id' => $plateColor->id,
            'front_plate_shape_id' => $frontPlateShape->id,
            'back_plate_shape_id' => $backPlateShape->id,
            'documents' => $files,
        ])
        ->assertStatus(200)
        ->assertJsonFragment(['status' => Status::submitted->name])
        ->assertJson(fn (AssertableJson $json) =>
            $json->hasAll(['id', 'amount', 'demands'])
                ->etc()
        );

    $cart = Cart::find($response['id']);
    $demands = $cart->demands;

    foreach ($demands as $demand) {
        $files = $demand->files;
        foreach ($files as $file)
        {
            $path = public_path($file['path']['path']);

            expect(file_exists($path))->toBeTrue();

            File::delete($path);

            expect(file_exists($path))->toBeFalse();
        }
    }

    // check that an order is created

    $numberOfOrdersAfterApiCall = Order::where('profile_id', $user['online_profile_id'])->count();
    // expect($numberOfOrdersAfterApiCall - $numberOfOrdersBeforeApiCall)->toBe(1);

});



test('User can validate cart during a standard immatriculation demand', function () {

    $vin = Arr::random($this->vins);
    $plateColor = PlateColor::inRandomOrder()->first();
    $frontPlateShape = PlateShape::inRandomOrder()->first();
    $backPlateShape = PlateShape::inRandomOrder()->first();

    foreach ($this->service->documents()->select('id')->get() as $document)
    {
        $files[] = [
            'type_id' => $document->id,
            'file' => UploadedFile::fake()->image('exampleimage.jpg')
        ];
    }


    $user = getUserWithProfile(ProfileTypesEnum::user->name);
    $numberOfOrdersBeforeApiCall = Order::where('profile_id', $user['online_profile_id'])->count();

    Log::debug('NPITEST175', [$user->identity->npi]);
    $response = actingAs($user)
        ->postJson('api/client/add-demand-to-cart', [
            'npi' => auth()->user()->username,
            'vin' => $vin,
            'service_id' => $this->service->id,
            'desired_number' => fake()->randomNumber(4, true),
            'label' => Str::random(8),
            'plate_color_id' => $plateColor->id,
            'front_plate_shape_id' => $frontPlateShape->id,
            'back_plate_shape_id' => $backPlateShape->id,
            'documents' => $files,
            ])
        ->assertStatus(200)
        ->assertJsonFragment(['status' => Status::submitted->name])
        ->assertJson(fn (AssertableJson $json) =>
            $json->hasAll(['id', 'amount', 'demands'])
                ->etc()
        );

    $cart_id = $response['id'];

    $response = actingAs($user)
        ->getJson('api/client/validate-cart'
        )
        ->assertStatus(200)
        // ->assertJsonFragment(['status' => Status::submitted->name])
        ->assertJson(fn (AssertableJson $json) =>
            $json->hasAll(['id', 'amount', 'demands'])
                ->etc()
        );

    $demand = Demand::find($response['demands'][0]['id']);
    $files = $demand->files;


    foreach ($files as $file)
    {
        $path = public_path($file['path']['path']);

        expect(file_exists($path))->toBeTrue();

        File::delete($path);

        expect(file_exists($path))->toBeFalse();
    }


    // the status of the cart is validated
    $cart_status = Cart::find($cart_id)->status;
    expect($cart_status)->toBe(Status::validated->name);

    $numberOfOrdersAfterApiCall = Order::where('profile_id', $user['online_profile_id'])->count();

    // an order is created
    expect($numberOfOrdersAfterApiCall - $numberOfOrdersBeforeApiCall)->toBe(1);
});


test('demand is automatically assigned from center to interpol', function () {

    $vin = Arr::random($this->vins);
    $plateColor = PlateColor::inRandomOrder()->first();
    $frontPlateShape = PlateShape::inRandomOrder()->first();
    $backPlateShape = PlateShape::inRandomOrder()->first();

    foreach ($this->service->documents()->select('id')->get() as $document)
    {
        $files[] = [
            'type_id' => $document->id,
            'file' => UploadedFile::fake()->image('exampleimage.jpg')
        ];
    }


    $user = getUserWithProfile(ProfileTypesEnum::user->name);
    Log::debug('USERTEST', [$user]);
    Log::debug('IDENTITYTEST', [$user->identity]);

    $response = actingAs($user)
        ->postJson('api/client/add-demand-to-cart', [
            'npi' => $user->identity->npi,
            'vin' => $vin,
            'service_id' => $this->service->id,
            'desired_number' => fake()->randomNumber(4, true),
            'label' => Str::random(8),
            'plate_color_id' => $plateColor->id,
            'front_plate_shape_id' => $frontPlateShape->id,
            'back_plate_shape_id' => $backPlateShape->id,
            'documents' => $files,
            ])
        ->assertStatus(200)
        ->assertJsonFragment(['status' => Status::submitted->name])
        ->assertJson(fn (AssertableJson $json) =>
            $json->hasAll(['id', 'amount', 'demands'])
                ->etc()
        );

        $response = actingAs($user)
        ->getJson('api/client/validate-cart'
        )
        ->assertStatus(200)
        // ->assertJsonFragment(['status' => Status::submitted->name])
        ->assertJson(fn (AssertableJson $json) =>
            $json->hasAll(['id', 'amount', 'demands'])
                ->etc()
        );

    $order = Order::find($response['id']);
    $transaction = $order->transaction;

    // Simulate paying the transaction
    $response = actingAs($user)
    ->postJson('api/client/submit-order', [
        'payment_reference' => $this->paymentReference,
        'order_id' => $order->id
    ])
    ->assertStatus(200);

    $demand = $order->demands->first();

    $userAnatt = getUserWithProfile(ProfileTypesEnum::anatt->name);
    Log::debug('USERANATTTEST', [$userAnatt]);

    $response = actingAs($userAnatt)
        ->postJson('api/assign-demand-to-center', ['demand_id' => $demand->id])
        ->assertStatus(200);

    sleep(2);
    $demand = Demand::find($response['demand']['id']);
    expect($demand->status)->toBe(Status::assigned_to_center->name);

    Log::debug('DEMANDTEST', [Demand::find($demand->id)]);
    $response = $response = actingAsProfile(ProfileTypesEnum::anatt->name)
    ->postJson('api/assign-demand-to-service', ['demand_id' => $demand->id])
    ->assertStatus(200);

    $response = $response = actingAsProfile(ProfileTypesEnum::anatt->name)
    ->postJson('api/assign-demand-to-staff', ['demand_id' => $demand->id])
    ->assertStatus(200);

    $response = $response = actingAsProfile(ProfileTypesEnum::anatt->name)
    ->postJson('api/validate-demand', ['demand_id' => $demand->id])
    ->assertStatus(200);
    expect($response['demand']['status'])->toBe(Status::pre_validated->name);

    $response = $response = actingAsProfile(ProfileTypesEnum::anatt->name)
    ->postJson('api/validate-demand', ['demand_id' => $demand->id])
    ->assertStatus(200);
    expect($response['demand']['status'])->toBe(Status::validated->name);

    Log::debug('VALIDATETEST', [$response]);

    $activeTreatmentId = $response['demand']['active_treatment_id'];
    $response = actingAsProfile(ProfileTypesEnum::anatt->name)
    ->postJson('api/verify-demand', ['treatment_id' => $activeTreatmentId])
    ->assertStatus(200);
    expect($response['demand']['status'])->toBe(Status::verified->name);

    Log::debug('VERIFYTEST', [$response]);

    $response = $response = actingAsProfile(ProfileTypesEnum::anatt->name)
    ->postJson('api/assign-demand-to-interpol', ['demand_id' => $demand->id])
    ->assertStatus(200);
    expect($response['demand']['status'])->toBe(Status::affected_to_interpol->name);

    Log::debug('ASSIGNTOINTERPOLTEST', [$response]);

    $response = actingAsProfile(ProfileTypesEnum::anatt->name)
    ->postJson('api/assign-demand-to-interpol-staff', [
        'demand_id' => $response['demand']['id']
    ])
    ->assertStatus(200);

    $interpolStaffId = $response['demand']['active_treatment']['interpol_staff_id'];
    $interpolStaffProfile = Profile::find($interpolStaffId);
    $interpolStaffUser = $interpolStaffProfile->user;
    $interpolStaffUser['online_profile_id'] = $interpolStaffId;

    Log::debug('ASSIGNTOINTERPOLSTAFFTEST', [$response]);
    expect($response['demand']['status'])->toBe(Status::assigned_to_interpol_staff->name);


    $response = actingAsProfile(ProfileTypesEnum::anatt->name)
    ->postJson('api/validate-demand', ['demand_id' => $demand->id])
    ->assertStatus(200);
    expect($response['demand']['status'])->toBe(Status::validated->name);

    $response = actingAsProfile(ProfileTypesEnum::anatt->name)
    ->postJson('api/validate-demand', ['demand_id' => $demand->id])
    ->assertStatus(200);
    expect($response['demand']['status'])->toBe(Status::validated->name);

    Log::debug('VALIDATETEST', [$response]);

    $response = actingAsProfile(ProfileTypesEnum::anatt->name, null, 'emit-print-order')
    ->postJson('api/emit-print-order', [
        'demand_id' => $response['demand']['id']
    ])
    ->assertStatus(201);

    $printOrderReference = $response['reference'];

    Log::debug('EMITPRINTORDERTEST', [$printOrderReference]);

    // anatt
    //     importer plaques: plates/create (excel) -> Admin\Config\PlateController@store

    $response = actingAsProfile(ProfileTypesEnum::anatt->name, null, 'emit-print-order')
    ->get('api/plates/create')
    ->assertStatus(200);

    // $platesFileUrl = $response->content();
    // $file = Http::get($platesFileUrl)->body();


    // $response = actingAsProfile(ProfileTypesEnum::anatt->name, null, 'emit-print-order')
    // ->get($response->content());
    // ->assertStatus(200);


    // agréé
    //     commande de plaque: plate-orders (store)
    // anatt
    //     valider comande: plate-orders/confirm
    // agréé
    //     impression de plaque: print-orders/search?q=$reference --> id print order
    //     otp --> authorization_id
    //         /client/send-demand-otp (npi/ifu du demandeur) --> authorization_id
    //         /client/verify-demand-otp (code(1234), authorization_id)

    //     print-orders/confirm-affectation (id print order, authorization_id)
    //     print-orders/print-plate
    // anatt
    //     plate-orders/confirmation-file-format --> excel
    //     valider impression de plaque: plate-orders/confirm (excel)

});

