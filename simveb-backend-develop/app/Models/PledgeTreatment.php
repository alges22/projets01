<?php

namespace App\Models;

use App\Models\Auth\Profile;
use App\Models\Institution\Institution;
use App\Traits\HasFiles;
use App\Traits\HasStatusLabel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PledgeTreatment extends Model
{
    use HasFactory, HasFiles, HasUuids, HasStatusLabel;

    protected $fillable = [
        'status',
        'treated_by',
        'treated_by_space',
        'institution_emitted_id',
        'institution_remitted_id',
        'institution_treat_id',
        'pledge_id',
        'emitted_at',
        'remitted_at',
        'validated_at',
        'rejected_at',
        'rejected_reasons',
        'affected_to_clerk',
        'affected_to_clerk_at',
        'affected_to_institution',
        'affected_to_institution_at',
        'affected_to_anatt',
        'affected_to_anatt_at',
    ];

    protected $appends = [
        'pledge_file'
    ];

    public function getPledgeFileAttribute()
    {
        if ($this->files()->exists()) {
            return $this->files()->get()->map(function ($file) {
                return asset($file->path['path']);
            });
        }

        return null;
    }

    public static function relations(): array
    {
        return [
            'profile',
            'institution:id,acronym,name,ifu,email,telephone,address',
        ];
    }

    public function treatedBy(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'treated_by');
    }

    public function pledge(): BelongsTo
    {
        return $this->belongsTo(Pledge::class);
    }

    public function institutionTreat(): BelongsTo
    {
        return $this->belongsTo(Institution::class, 'institution_treat_id');
    }

    public function institutionEmit(): BelongsTo
    {
        return $this->belongsTo(Institution::class, 'institution_emitted_id');
    }

    public function institutionRemit(): BelongsTo
    {
        return $this->belongsTo(Institution::class, 'institution_remitted_id');
    }

    public function affectedToClerk(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'affected_to_clerk');
    }

    public function affectedToInstitution(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'affected_to_institution');
    }

    public function affectedToAnatt(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'affected_to_anatt');
    }
}
