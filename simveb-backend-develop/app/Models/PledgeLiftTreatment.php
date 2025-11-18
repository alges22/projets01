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

class PledgeLiftTreatment extends Model
{
    use HasFactory, HasUuids, HasFiles, HasStatusLabel;

    protected $fillable = [
        'status',
        'rejected_reasons',
        'pledge_lift_id',
        'treated_by',
        'institution_treat_id',
        'treated_by_space',
        'institution_remitted_id',
        'affected_to_clerk',
        'affected_to_clerk_at',
        'affected_to_anatt',
        'affected_to_anatt_at',
        'emitted_at',
        'remitted_at',
        'validated_at',
        'rejected_at',
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
            //
        ];
    }

    public function pledgeLift(): BelongsTo
    {
        return $this->belongsTo(PledgeLift::class);
    }

    public function treatedBy(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'treated_by');
    }

    public function affectedToClerk(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'affected_to_clerk');
    }

    public function affectedToAnatt(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'affected_to_anatt');
    }

    public function institutionTreat(): BelongsTo
    {
        return $this->belongsTo(Institution::class, 'institution_treat_id');
    }

    public function institutionRemit(): BelongsTo
    {
        return $this->belongsTo(Institution::class, 'institution_remitted_id');
    }
}
