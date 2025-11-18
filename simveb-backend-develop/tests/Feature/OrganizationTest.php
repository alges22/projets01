<?php
use App\Consts\Roles;
use App\Enums\ProfileTypesEnum;
use App\Models\Config\Organization;


beforeEach(function () {
    $this->organization = Organization::factory()->create([
        'name' => 'Nautilus',
        'description' => 'Nautilus also known as Nautilus Technology is an IT company.',
    ]);
});

test('Anatt profile can access the list of organization', function () {
    actingAsProfile(ProfileTypesEnum::anatt->name, [Roles::ADMIN], 'browse-organization')
        ->get('/api/organizations')
        ->assertStatus(200);
});


test('Anatt profile can create an organization through the API', function () {
    actingAsProfile(ProfileTypesEnum::anatt->name, [Roles::ADMIN], 'store-organization')
        ->post('/api/organizations', [
            'name' => 'The Organization',
            'description' => 'An organization created for testing purposes'])
        ->assertStatus(200);
});

test('Anatt profile can read an organization through the API', function () {
    actingAsProfile(ProfileTypesEnum::anatt->name, [Roles::ADMIN], 'show-organization')
        ->get("/api/organizations/{$this->organization->id}")
        ->assertStatus(200)
        ->assertJsonFragment([
            'name' => $this->organization->name,
            'description' => $this->organization->description,
        ]);
});

test('Anatt profile can update an organization through the API', function () {
    actingAsProfile(ProfileTypesEnum::anatt->name, [Roles::ADMIN], 'update-organization')
        ->put("/api/organizations/{$this->organization->id}", [
            'name' => 'NTech',
            'description' => 'NTech',])
        ->assertStatus(200);
});


test('Anatt profile can delete an organization through the API', function () {
    actingAsProfile(ProfileTypesEnum::anatt->name, [Roles::ADMIN], 'delete-organization')
        ->delete("/api/organizations/{$this->organization->id}")
        ->assertStatus(200);
});

