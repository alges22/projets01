<?php

namespace App\Models\Account;

use App\Models\Auth\Invitation;
use App\Models\Auth\Profile;
use App\Models\Treatment\Treatment;
use App\Models\Vehicle\VehicleAdministrativeStatus;
use App\Traits\HasVerificationEmail;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\Exceptions\OAuthServerException;
use Laravel\Passport\HasApiTokens;
use Ntech\UserPackage\Models\HistoryPassword;
use Ntech\UserPackage\Models\Identity;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids, HasPermissions, HasRoles, HasVerificationEmail;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'identity_id',
        'email_verified_at',
        'disabled_at',
        'password',
        'online_profile_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    /**
     * Find the user instance for the given username.
     *
     * @param string $username
     * @return User
     * @throws OAuthServerException
     */
    public function findForPassport($username)
    {
        $user = $this->where('username', $username)->first();

        if ($user !== null && $user->disabled_at !== NULL) {
            throw new OAuthServerException('User account is not activated', 6, 'account_inactive', 401);
        }

        return $user;
    }

    public function routeNotificationForMail($notification)
    {
        // Return name and email address...
        return $this->username;
    }

    /**
     * @return HasMany
     */
    public function historyPasswords()
    {
        return $this->hasMany(HistoryPassword::class);
    }

    public function identity()
    {
        return $this->belongsTo(Identity::class);
    }


    public function interpolActiveTreatments()
    {
        return $this->hasMany(Treatment::class, 'interpol_staff_id')
            ->whereNull('closed_at');
    }

    public function declarant()
    {
        return $this->hasOne(Declarant::class);
    }

    public function vehicleAdministrativeStatus()
    {
        return $this->hasMany(VehicleAdministrativeStatus::class);
    }

    public function profiles()
    {
        return $this->hasMany(Profile::class);
    }

    public function onlineProfile()
    {
        return $this->belongsTo(Profile::class, 'online_profile_id');
    }

    public function getPendingInvitationsCountAttribute()
    {
        return Invitation::where([
            'npi' => $this->username,
            'accepted' => false,
            'denied' => false,
        ])->count();
    }

    protected static function newFactory(): Factory
    {
        return UserFactory::new();
    }
}
