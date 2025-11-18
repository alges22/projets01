<?php

namespace App\Models;

use App\Models\Auth\Profile;
use App\Traits\HasFiles;
use App\Traits\HasStatusLabel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OppositionTreatment extends Model
{
    use HasFactory, HasFiles, HasUuids, HasStatusLabel;

    protected $fillable = [
        'status',
        'treated_by',
        'rejected_reason',
        'institution_id',
        'opposition_id',
        'emitted_at',
        'remitted_at',
        'validated_at',
        'lifted_at',
        'rejected_at',
        'affected_to_clerk',
        'affected_to_judge',
        'affected_to_judge_at',
        'affected_to_clerk_at',
    ];

    protected $appends = [
        'opposition_file'
    ];


    public function getOppositionFileAttribute()
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
        ];
    }

    public function affectedToClerk(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'affected_to_clerk');
    }

    public function affectedToJudge(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'affected_to_judge');
    }
}
