<?php
use App\Consts\Roles;
use App\Enums\ProfileTypesEnum;
use App\Models\Auth\ProfileType;
use App\Models\Immatriculation\FormatComponent;
use App\Models\Immatriculation\ImmatriculationFormat;
use App\Models\Vehicle\VehicleCategory;
use App\Services\Immatriculation\ImmatriculationFormatService;

beforeEach(function () {
    $profileType = ProfileType::factory()->create();
    $components = [];
    foreach (FormatComponent::all() as $component) {
        $components[] = [
            'id' => $component->id,
            'value' => '',
            'position' => count($components) + 1,
        ];
    }
    $format = (new ImmatriculationFormatService())->generateFormatArray($components);

    $this->profileType = $profileType;
    $this->components = $components;
    $this->vehicleCategory = VehicleCategory::factory()->create();
    $this->secondVehicleCategory = VehicleCategory::factory()->create();
    $this->immatriculationFormat = ImmatriculationFormat::factory()->create([
        'format' => $format,
        'profile_type_id' => $profileType]);
});


test('Anatt Admin with browse-immatriculation-format permission can access the list of immatriculation format', function () {
    actingAsProfile(ProfileTypesEnum::anatt->name, [Roles::ADMIN], 'browse-immatriculation-format')
        ->get('/api/immatriculation-formats')
        ->assertStatus(200);
});


test('an immatriculation format can be created with expected outputs', function () {

    $immatriculationFormatsData = [
        [
            'vehicle_category_id' => VehicleCategory::where('nb_plate', 1)->first()->id,
            'profile_type_id' => ProfileType::where('code', ProfileTypesEnum::user->name)->first()->id,
            'components' => $this->components,
        ]
    ];

    foreach ($immatriculationFormatsData as $data) {

        if (!$immatriculationFormat = ImmatriculationFormat::where('vehicle_category_id', $data['vehicle_category_id'])->where('profile_type_id', $data['profile_type_id'])->first()) {
            $format = (new ImmatriculationFormatService())->generateFormatArray($data['components']);
            $immatriculationFormat = ImmatriculationFormat::factory()->create([
                'vehicle_category_id' => $data['vehicle_category_id'],
                'profile_type_id' => $data['profile_type_id'],
                'format' => $format,
            ]);

            foreach ($data['components'] as $component) {
                $immatriculationFormat->components()->attach([$component['id'] =>
                [
                    'value' => $component['value'] ?? null,
                    'position' => $component['position']
                ]]);
            }
        }

        expect($immatriculationFormat)->toBeInstanceOf(ImmatriculationFormat::class);
        expect($immatriculationFormat->vehicle_category_id)->toBe($data['vehicle_category_id']);
        expect($immatriculationFormat->profile_type_id)->toBe($data['profile_type_id']);
        expect($immatriculationFormat->format)->toBe($format);
    }

});

test('Anatt Admin with store-immatriculation-format permission can create an immatriculation format through the API', function () {
    actingAsProfile(ProfileTypesEnum::anatt->name, [Roles::ADMIN], 'store-immatriculation-format')
        ->post('/api/immatriculation-formats', [
            'vehicle_category_id' => $this->vehicleCategory->id,
            'profile_type_id' => ProfileType::factory()->create()->id,
            'components' => $this->components])
        ->assertStatus(200);
});

test('Anatt Admin with show-immatriculation-format permission can read an immatriculation format through the API', function () {
    actingAsProfile(ProfileTypesEnum::anatt->name, [Roles::ADMIN], 'show-immatriculation-format')
        ->get("api/immatriculation-formats/{$this->immatriculationFormat->id}")
        ->assertStatus(200)
        ->assertJsonFragment([
            'vehicle_category_id' => $this->immatriculationFormat->vehicle_category_id,
            'profile_type_id' => $this->immatriculationFormat->profile_type_id,
            'format' => $this->immatriculationFormat->format,
        ]);
});

test('Anatt Admin with update-immatriculation-format permission can update an immatriculation format through the API', function () {
    actingAsProfile(ProfileTypesEnum::anatt->name, [Roles::ADMIN], 'update-immatriculation-format')
        ->put("api/immatriculation-formats/{$this->immatriculationFormat->id}", [
            'vehicle_category_id' => $this->secondVehicleCategory->id,
            'profile_type_id' =>  ProfileType::factory()->create()->id,
            'components' => $this->components])
        ->assertStatus(200);
});


test('Anatt Admin with delete-immatriculation-format permission can delete an immatriculation format through the API', function () {
    actingAsProfile(ProfileTypesEnum::anatt->name, [Roles::ADMIN], 'delete-immatriculation-format')
        ->delete("api/immatriculation-formats/{$this->immatriculationFormat->id}")
        ->assertStatus(200);
});

