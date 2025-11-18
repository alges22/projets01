<?php

namespace Ntech\UserPackage\Models;

use App\Models\Account\Declarant;
use App\Models\Account\User;
use App\Models\Config\Country;
use App\Models\Config\District;
use App\Models\Config\State;
use App\Models\Config\Town;
use App\Models\Config\Village;
use App\Models\Vehicle\VehicleOwner;
use App\Traits\SecureDelete;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Ntech\ActivityLogPackage\Traits\ActivityTrait;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Identity extends Model
{
    use HasFactory, HasUuids, Notifiable, ActivityTrait, LogsActivity, SecureDelete, Filterable, SoftDeletes;

    protected $casts = [
        'photo' => 'array'
    ];

    protected $appends = [
        'fullName'
    ];

    protected $fillable = [
        'email',
        'firstname',
        'lastname',
        'telephone',
        'birthdate',
        'birth_place',
        'country_id',
        'town_id',
        'house',
        'photo',
        'profession_id',
        'address',
        'gender',
        'npi',
        'telephone_fix',
        'telephone_professional',
        'education_level',
        'children',
        'activity_id',
        'social_category',
        'matrimonial_status',
        'ifu',
        'state_id',
        'district_id',
        'village_id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    private function getEntityName() : string
    {
        return "Identité";
    }

    static function relations()
    {
        return [];
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }
    public function town()
    {
        return $this->belongsTo(Town::class);
    }

    public function staff()
    {
        return $this->hasOne(Staff::class);
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function space()
    {
        return null;
    }

    public function getGlobalIdentifierKeyName(): string
    {
        return "id";
    }

    public function getGlobalIdentifierKey()
    {
        return $this->getAttribute($this->getGlobalIdentifierKeyName());
    }


    public function getSyncedAttributeNames(): array
    {
        return  $this->attributes;
    }


    public function getFullNameAttribute(): string
    {
        return $this->attributes['lastname']. ' '.$this->attributes['firstname'];
    }

    public function routeNotificationForMail($notification)
    {
        return $this->email;
    }

    public function routeNotificationForSms()
    {
        return $this->telephone;
    }

    public function declarant()
    {
        return $this->hasOne(Declarant::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function village()
    {
        return $this->belongsTo(Village::class);
    }

    public function vehicleOwner()
    {
        return $this->hasOne(VehicleOwner::class);
    }
}
