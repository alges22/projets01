<?php
use App\Consts\Roles;
use App\Enums\ProfileTypesEnum;
use App\Enums\Status;
use App\Models\Config\Service;
use App\Models\Config\ServiceType;


beforeEach(function () {
    $serviceType = ServiceType::factory()->create();
    $service = Service::factory()->create();

    $this->serviceType = $serviceType;
    $this->service = $service;
});


test('Anatt Admin user can access the list of services', function () {
    actingAsProfile(ProfileTypesEnum::anatt->name, [Roles::ADMIN], 'browse-service')
        ->get('/api/services')
        ->assertStatus(200);
});


test('A service can be created with expected outputs', function () {
    $service = Service::factory()->create([
        'code' => 'TEST_SERVICE',
        'name' => 'Test service',
        'description' => 'A service created for testing purpose',
        'type_id' => $this->serviceType->id,
    ]);

    expect($service)->toBeInstanceOf(Service::class);
    expect($service->code)->toBe('TEST_SERVICE');
    expect($service->name)->toBe('Test service');
    expect($service->description)->toBe('A service created for testing purpose');
    expect($service->type_id)->toBe($this->serviceType->id);
});

test('Anatt Admin user with store-service permission can create a service through the API', function () {
    $data = [
        'code' => 'SERVICE_TEST_CREATION_THROUGH_API',
        'name' => 'Service test creation through api',
        'type_id'  => $this->serviceType->id,
        'description' => 'A service to test post creation of new services through the api',
        'duration' => null,
        'cost' => 2000,
        'procedures' => 'Procédure',
        'who_can_apply' => null,
        'link' => null,
        'color' => null,
        'image' => null,
        'status' => Status::validated->name,
        'extract' => null,
        'children' => null,
        'parent_service_id' => null,
        'documents' => null,
        'vehicle_category_id' => null,
        'steps' => null,
        'extra_services' => null,
        'owner_price_variations' => null,
        'category_price_variations' => null,
        'characteristic_price_variations' => null,
    ];
    actingAsProfile(ProfileTypesEnum::anatt->name, [Roles::ADMIN], 'store-service')
        ->post('/api/services', $data)
        ->assertStatus(200);
});

test('Anatt Admin user with show-service permission can read an immatriculation format through the API', function () {
    actingAsProfile(ProfileTypesEnum::anatt->name, [Roles::ADMIN], 'show-service')
        ->get("api/services/{$this->service->id}")
        ->assertStatus(200);
});

test('Anatt Admin user with update-service permission can update a service through the API', function () {

    $data = [
        'code' => 'SERVICE_TEST_UPDATE_THROUGH_API',
        'name' => 'Service test update through api',
        'type_id'  => $this->serviceType->id,
        'description' => 'A service to test post update of new services through the api',
        'duration' => null,
        'cost' => 2000,
        'procedures' => 'Procédure',
        'who_can_apply' => null,
        'link' => null,
        'color' => null,
        'image' => null,
        'status' => Status::validated->name,
        'extract' => null,
        'children' => null,
        'parent_service_id' => null,
        'documents' => null,
        'vehicle_category_id' => null,
        'steps' => null,
        'extra_services' => null,
        'owner_price_variations' => null,
        'category_price_variations' => null,
        'characteristic_price_variations' => null,

    ];

    actingAsProfile(ProfileTypesEnum::anatt->name, [Roles::ADMIN], 'update-service')
        ->put("api/services/{$this->service->id}", $data)
        ->assertStatus(200);
});


test('Anatt Admin user with delete-service permission can delete a service through the API', function () {
    actingAsProfile(ProfileTypesEnum::anatt->name, [Roles::ADMIN], 'delete-service')
        ->delete("api/services/{$this->service->id}")
        ->assertStatus(200);
});

