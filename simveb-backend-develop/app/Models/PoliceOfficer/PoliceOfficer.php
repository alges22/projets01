<?php

namespace App\Models\PoliceOfficer;

use App\Models\Auth\Profile;
use App\Models\Config\Border;
use App\Traits\SecureDelete;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Ntech\ActivityLogPackage\Traits\ActivityTrait;
use Ntech\UserPackage\Models\Identity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PoliceOfficer extends Model
{
    use HasFactory,
    HasUuids,
    ActivityTrait,
    LogsActivity,
    Notifiable,
    Filterable,
    SecureDelete,
    SoftDeletes;

    protected $fillable = [
        'border_id',
        'identity_id',
        'profile_id'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    private function getEntityName(): string
    {
        return "Officier de police";
    }

    public static function relations()
    {
        return [
            'border:id,name,border_country_id',
            'border.country:id,name',
            'profile:id,type_id',
            'profile.type:id,code,name',
            'profile.roles',
            'identity:id,firstname,lastname,email,telephone',
            'identity.user:id,identity_id,email',
        ];
    }

    /**
     * Get the identity of the police officer
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function identity(): BelongsTo
    {
        return $this->belongsTo(Identity::class);
    }

    /**
     * Get the profile of the police officer
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    /**
     * Get the border where the police officer is affected
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function border(): BelongsTo
    {
        return $this->belongsTo(Border::class);
    }
}
