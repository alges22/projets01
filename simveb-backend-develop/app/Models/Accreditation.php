<?php

namespace App\Models;

use App\Models\Auth\Profile;
use App\Traits\SecureDelete;
use App\Traits\HasStatusLabel;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\ExactFilter;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Spatie\Activitylog\LogOptions;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ntech\ActivityLogPackage\Traits\ActivityTrait;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Accreditation extends Model implements CanFilterContract
{
    use HasFactory,
        HasUuids,
        HasStatusLabel,
        LogsActivity,
        ActivityTrait,
        Filterable,
        SecureDelete,
        SoftDeletes;

    protected $fillable = [
        'status',
        'rejected_reason',
        'receiver_id',
        'author_id',
        'validator_id',
        'rejector_id',
        'authored_at',
        'validated_at',
        'rejected_at'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    private function getEntityName(): string
    {
        return "Accréditation";
    }

    public function scopeSearch($query, $keyword)
    {
        return $query->where(function ($query) use ($keyword) {
            $query->whereHas('receiver', function ($q) use ($keyword) {
                $q->whereRelation('identity', 'npi', 'like', "%$keyword%")
                    ->orWhereHas('identity', function ($q) use ($keyword) {
                        $keyword = strtolower($keyword);
                        $q->whereRaw("CONCAT(LOWER(firstname), ' ',LOWER(lastname)) LIKE ?", ["%$keyword%"])
                            ->orWhereRaw("CONCAT(LOWER(lastname), ' ',LOWER(firstname)) LIKE ?", ["%$keyword%"]);
                    });
            })
                ->orWhereHas('author', function ($q) use ($keyword) {
                    $q->whereHas('identity', function ($q) use ($keyword) {
                        $keyword = strtolower($keyword);
                        $q->whereRaw("CONCAT(LOWER(firstname), ' ',LOWER(lastname)) LIKE ?", ["%$keyword%"])
                            ->orWhereRaw("CONCAT(LOWER(lastname), ' ',LOWER(firstname)) LIKE ?", ["%$keyword%"]);
                    });
                });
        });
    }

    /**
     *
     */
    static function relations(): array
    {
        return [
            'receiver.identity:id,firstname,lastname',
            'receiver.institution:id,name',
            'receiver.type:id,name',
            'roles:id,name,label',
            'permissions:id,name,label',
            'author.identity:id,firstname,lastname',
            'validator.identity:id,firstname,lastname',
            'rejector.identity:id,firstname,lastname',
        ];
    }

    /**
     *
     */
    static function secureDeleteRelations(): array
    {
        return [
            'roles',
            'permissions',
        ];
    }

    /**
     * Get the profile that received the accreditation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'receiver_id');
    }

    /**
     * Get the profile that ask for the accreditation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'author_id');
    }

    /**
     * Get the profile that validates the accreditation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function validator(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'validator_id');
    }

    /**
     * Get the profile that reject the accreditation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rejector(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'rejector_id');
    }

    /**
     * Get all of the roles that are assigned to this accreditation.
     */
    public function roles()
    {
        return $this->morphedByMany(Role::class, 'accreditable');
    }

    /**
     * Get all of the permissions that are assigned to this accreditation.
     */
    public function permissions()
    {
        return $this->morphedByMany(Permission::class, 'accreditable');
    }

    /**
     *
     */
    public function getFilters(): array
    {
        return [
            new Sort(),
            new ScopeFilter('search'),
            new ExactFilter('status'),
        ];
    }
}
