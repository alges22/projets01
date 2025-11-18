<?php

namespace App\Models\Space;

use App\Enums\Status;
use App\Models\Auth\Profile;
use App\Models\Auth\ProfileType;
use App\Models\Institution\Institution;
use App\Traits\HasFiles;
use App\Traits\HasStatusLabel;
use App\Traits\HasWallet;
use App\Traits\SecureDelete;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\ExactFilter;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Ntech\ActivityLogPackage\Traits\ActivityTrait;
use Ntech\RequiredDocumentPackage\Traits\HasRequiredDocumentTypes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Space extends Model implements CanFilterContract
{
    use HasFactory, HasUuids, ActivityTrait, LogsActivity, SecureDelete, Filterable, SoftDeletes, HasFiles, HasRequiredDocumentTypes, Notifiable, HasWallet, HasStatusLabel;

    protected $fillable = [
        'validator_id',
        'request_id',
        'profile_type_id',
        'institution_id',
        'template',
        'status',
    ];

    protected $appends = ['type_label', 'has_suspension', 'has_lifting', 'current_suspension', 'current_lifting'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    public function getFilters(): array
    {
        return [
            new ScopeFilter('search'),
            new Sort(),
            new ExactFilter('profile_type_id'),
            new ExactFilter('institution_id'),
            new ExactFilter('request_id'),
        ];
    }

    public function scopeSearch(Builder $query, string $keyword)
    {
        $keyword = strtolower(trim($keyword));

        return $query->where(function ($query) use ($keyword) {
            return $query->whereHas('profileType', function (Builder $query)  use ($keyword) {
                $query->whereRaw("LOWER(name) LIKE ?", ["%$keyword%"]);
            })->orWhereHas('institution', function (Builder $query) use ($keyword) {
                $query->whereRaw("LOWER(name) LIKE ?", ["%{$keyword}%"]);
            });
        });
    }

    public function registrationRequest()
    {
        return $this->belongsTo(SpaceRegistrationRequest::class, 'request_id');
    }

    private function getEntityName(): string
    {
        return "Espace";
    }

    static function relations()
    {
        return [
            'institution:id,name',
            'profileType:id,code,name',
            'profiles:id,space_id,user_id',
            'profiles.user:id,username,email,identity_id',
            'profiles.user.identity',
            'files',
        ];
    }

    static function secureDeleteRelations()
    {
        return [
            'files',
            'profiles',
        ];
    }

    public function profileType()
    {
        return $this->belongsTo(ProfileType::class, 'profile_type_id');
    }

    public function profiles()
    {
        return $this->hasMany(Profile::class);
    }

    public function getTypeLabelAttribute()
    {
        return $this->profileType?->name;
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function suspensionRequests()
    {
        return $this->hasMany(SpaceSuspensionRequest::class);
    }

    public function suspensionLiftingRequests()
    {
        return $this->hasMany(SpaceSuspensionLiftingRequest::class);
    }

    public function getHasSuspensionAttribute()
    {
        return $this->suspensionRequests()->where('status', Status::pending->name)->exists();
    }

    public function getHasLiftingAttribute()
    {
        return $this->suspensionLiftingRequests()->where('status', Status::pending->name)->exists();
    }

    public function getCurrentSuspensionAttribute()
    {
        return $this->suspensionRequests()->where('status', Status::pending->name)->latest()->first();
    }

    public function getCurrentLiftingAttribute()
    {
        return $this->suspensionLiftingRequests()->where('status', Status::pending->name)->latest()->first();
    }
}
