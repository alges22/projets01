<?php

namespace App\Models\Order;

use App\Models\Institution\Institution;
use App\Models\Auth\Profile;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ntech\ActivityLogPackage\Traits\ActivityTrait;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Cart extends Model
{
    use HasFactory,
        HasUuids,
        ActivityTrait,
        LogsActivity;

    protected $fillable = [
        'profile_id', 'institution_id', 'amount', 'status'
    ];

    protected $casts = ['amount' => 'double'];

    protected $with = ['demands'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }


    private function getEntityName() : string
    {
        return 'Panier';
    }

    public static function relations()
    {
        return [
            'profile',
            'institution'
        ];
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function demands()
    {
        return $this->belongsToMany(Demand::class)->withPivot(['amount']);
    }

    public function getExtraServicesAttribute()
    {
        $extraServices = [];

        foreach ($this->demands as $demand) {
            foreach ($demand->service->serviceExtraServices as $extraService) {
                $extraServices[] = $extraService;
            }
        }
        foreach ($this->demands as $demand) {
            unset($extraServices[array_search($demand->service, $extraServices)]);
        }

        return array_unique($extraServices);
    }
}
