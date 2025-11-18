<?php
use App\Models\Institution\InstitutionType;

test('can create an institution type', function () {
    $name = 'Police';
    $description = 'Police aux frontières';
    $institutionType = new InstitutionType([
        'name' => $name,
        'description' => $description
    ]);
    expect($institutionType->name)->toBe($name);
    expect($institutionType->description)->toBe($description);
});
