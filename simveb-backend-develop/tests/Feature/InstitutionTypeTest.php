<?php
use App\Consts\Roles;
use App\Enums\ProfileTypesEnum;

test('Anatt Admin with store-institution-type permission can access the list of institution types with an authenticated user', function () {
    actingAsProfile(ProfileTypesEnum::anatt->name, [Roles::ADMIN], 'store-institution-type')
        ->get('/api/institution-types')
        ->assertStatus(200);
});

