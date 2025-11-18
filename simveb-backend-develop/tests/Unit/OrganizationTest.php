<?php
use App\Models\Config\Organization;

test('Anatt profile can create an organization', function () {
    $organization = new Organization([
        'name' => 'Test Organization',
        'description' => 'The test Organization created with a factory'
    ]);

    expect($organization)->toBeInstanceOf(Organization::class);
    expect($organization->name)->toBe('Test Organization');
    expect($organization->description)->toBe('The test Organization created with a factory');
});
