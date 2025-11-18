<?php
use App\Enums\ProfileTypesEnum;
use App\Models\Auth\Profile;
use function Pest\Laravel\{actingAs};
use App\Consts\Roles;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

uses(
    Tests\TestCase::class,
    // Illuminate\Foundation\Testing\RefreshDatabase::class,
)->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/


function getUserWithProfile($profileType, $roles=null, $permission=null)
{
    $profiles = Profile::whereHas('type', function ($query) use($profileType) {
        $query->where('code', $profileType);
    })->get();

    $profileWithRequestedRolesAndPermission = null;
    if ($roles) {
        foreach ($profiles as $profile) {
            if (empty(array_diff($roles, $profile->roles->pluck('name')->toArray()))) {
                if ($permission) {
                    if ($profile->hasPermissionTo($permission)) {
                        $profileWithRequestedRolesAndPermission = $profile;
                        break;
                    } else {
                        continue;
                    }
                } else {
                    $profileWithRequestedRolesAndPermission = $profile;
                    break;
                }
            }
        }
    } else {
        if ($permission) {
            foreach ($profiles as $profile) {
                if ($profile->hasPermissionTo($permission)) {
                    $profileWithRequestedRolesAndPermission = $profile;
                    break;
                }
            }
        } else {
            $profileWithRequestedRolesAndPermission = $profiles->random();
        }
    }

    $user = $profileWithRequestedRolesAndPermission->user;
    $user['online_profile_id'] = $profileWithRequestedRolesAndPermission->id;

    return $user;
}



function actingAsProfile($profileType, $roles=null, $permission=null)
{
    $user = getUserWithProfile($profileType, $roles, $permission);

    return actingAs($user);
}
