<?php

namespace App\Models\Space;

use App\Models\Auth\Profile;
use App\Traits\HasStatusLabel;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\ExactFilter;
use Baro\PipelineQueryCollection\RelativeFilter;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ntech\ActivityLogPackage\Traits\ActivityTrait;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Str;

class SpaceSuspensionLiftingRequest extends Model implements CanFilterContract
{
    use HasFactory, HasUuids, ActivityTrait, LogsActivity, Filterable, HasStatusLabel;

    protected $fillable = [
        'reference',
        'author_id',
        'space_id',
        'status',
        'validator_id',
        'rejector_id',
        'validated_at',
        'rejected_at',
        'reject_reason',
    ];

    public function author()
    {
        return $this->belongsTo(Profile::class, 'author_id');
    }

    public function space()
    {
        return $this->belongsTo(Space::class);
    }

    public function validator()
    {
        return $this->belongsTo(Profile::class, 'validator_id');
    }

    public function rejector()
    {
        return $this->belongsTo(Profile::class, 'rejector_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    public function getFilters(): array
    {
        return [
            new ScopeFilter('search'),
            new Sort(),
            new ExactFilter('author_id'),
            new ExactFilter('validator_id'),
            new ExactFilter('rejector_id'),
            new RelativeFilter('status'),
        ];
    }

    public function scopeSearch($query, string $keyword)
    {
        $keyword = strtolower($keyword);

        return $query->where(function ($query) use ($keyword) {
            return $query->whereRaw("LOWER(reference) LIKE ?", ["%$keyword%"])
                ->orWhereRelation('space', 'name', 'LIKE', "%$keyword%");
        });
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->reference = 'SSLR-' . now()->timestamp . strtoupper(Str::random(3));
        });
    }

    static function relations(): array
    {
        return [
            'space:id,institution_id,profile_type_id,status',
            'space.institution:id,name',
            'space.profileType:id,name',
            'author:id,identity_id',
            'author.identity:id,firstname,lastname',
            'validator:id,identity_id',
            'validator.identity:id,firstname,lastname',
            'rejector:id,identity_id',
            'rejector.identity:id,firstname,lastname',
        ];
    }

    public function getEntityName(): string
    {
        return "Demande de levée de suspension d'espace";
    }
}
