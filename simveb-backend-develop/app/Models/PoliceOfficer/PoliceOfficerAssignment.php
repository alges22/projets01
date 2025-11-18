<?php

namespace App\Models\PoliceOfficer;

use App\Models\Auth\Profile;
use App\Models\Config\Border;
use App\Traits\HasStatusLabel;
use App\Traits\SecureDelete;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PoliceOfficerAssignment extends Model implements CanFilterContract
{
    use HasFactory, HasUuids, HasStatusLabel, Filterable, SecureDelete, SoftDeletes;

    protected $fillable = [
        'border_id',
        'profile_id',
        'status',
        'reject_reason',
        'author_id',
        'validator_id',
        'rejector_id',
        'authored_at',
        'validated_at',
        'rejected_at'
    ];

    protected $casts = [
        'authored_at' => 'datetime',
        'validated_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    static function relations(): array
    {
        return [
            'border',
            'profile.identity',
            'author.identity',
            'validator.identity',
            'rejector.identity'
        ];
    }

    public function getfilters()
    {
        return [
            new Sort(),
        ];
    }

    static function secureDeleteRelations(): array
    {
        return [
            //
        ];
    }

    /**
     * Get the border that owns the PoliceOfficerAssignment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function border(): BelongsTo
    {
        return $this->belongsTo(Border::class);
    }

    /**
     * Get the officer that owns the PoliceOfficerAssignment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    /**
     * Get the author of the PoliceOfficerAssignment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'author_id');
    }

    /**
     * Get the validator that owns the PoliceOfficerAssignment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function validator(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'validator_id');
    }

    /**
     * Get the rejector that owns the PoliceOfficerAssignment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rejector(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'rejector_id');
    }
}
