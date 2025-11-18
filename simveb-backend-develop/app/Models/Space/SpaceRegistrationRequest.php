<?php

namespace App\Models\Space;

use App\Enums\SpaceTypesEnum;
use App\Models\Auth\Profile;
use App\Models\Auth\ProfileType;
use App\Models\Institution\Institution;
use App\Traits\HasFiles;
use App\Traits\HasStatusLabel;
use App\Traits\SecureDelete;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\ExactFilter;
use Baro\PipelineQueryCollection\RelativeFilter;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ntech\ActivityLogPackage\Traits\ActivityTrait;
use Ntech\RequiredDocumentPackage\Traits\HasRequiredDocumentTypes;
use Ntech\UserPackage\Models\Identity;
use ReflectionEnumBackedCase;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class SpaceRegistrationRequest extends Model implements CanFilterContract
{
    use HasFactory, HasUuids, ActivityTrait, LogsActivity, SecureDelete, Filterable, SoftDeletes, HasFiles, HasRequiredDocumentTypes, HasStatusLabel;

    protected $fillable = [
        'type',
        'profile_type_id',
        'institution_id',
        'first_member_npi',
        'status',
        'validated_at',
        'validator_id',
        'rejected_at',
        'rejector_id',
        'reject_reason',
        'creator_id',
        'template',
    ];

    protected $appends = ['type_label'];

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
            new RelativeFilter('status'),
        ];
    }

    public function scopeSearch(Builder $query, string $keyword)
    {
        $keyword = trim($keyword);

        return $query->where(function (Builder $query)  use ($keyword) {
            $query->whereRelation('institution', 'name', 'like', "%$keyword%")
                ->orWhereRelation('profileType', 'name', 'like', "%$keyword%")
                ->orWhere('first_member_npi', 'like', "%$keyword%");
        });
    }

    public function creator()
    {
        return $this->belongsTo(Profile::class, 'creator_id');
    }

    public function validator()
    {
        return $this->belongsTo(Profile::class, 'validator_id');
    }

    public function rejector()
    {
        return $this->belongsTo(Profile::class, 'rejector_id');
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    private function getEntityName(): string
    {
        return "Demande d'enregistrement d'affilié";
    }

    public function identity()
    {
        return $this->belongsTo(Identity::class);
    }

    static function relations()
    {
        return [
            'profileType:id,name',
            'institution:id,name',
            'creator.identity:id,firstname,lastname,telephone,email,npi',
            'files',
            'validator.identity:id,firstname,lastname,telephone,email,npi',
            'rejector.identity:id,firstname,lastname,telephone,email,npi',
        ];
    }

    static function secureDeleteRelations()
    {
        return [
            'files',
        ];
    }

    public function profileType()
    {
        return $this->belongsTo(ProfileType::class, 'profile_type_id');
    }

    public function getTypeLabelAttribute()
    {
        if (isset($this->attributes['type'])) {
            $reflection = new ReflectionEnumBackedCase(SpaceTypesEnum::class, $this->attributes['type']);

            return $reflection->getValue();
        }

        return '';
    }
}
