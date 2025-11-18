<?php

namespace App\Models\Auction;

use App\Models\Institution\Institution;
use App\Models\Reform\ReformDeclaration;
use App\Models\Vehicle\Vehicle;
use App\Traits\SecureDelete;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\ExactFilter;
use Baro\PipelineQueryCollection\Sort;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ntech\ActivityLogPackage\Traits\ActivityTrait;
use Ntech\UserPackage\Models\Identity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class AuctionSaleVehicle extends Model implements CanFilterContract
{
    use HasFactory, HasUuids, Filterable, LogsActivity, SecureDelete, ActivityTrait;

    protected $fillable = [
        'auction_sale_declaration_id',
        'vehicle_id',
        'price',
        'buyer_npi',
        'buyer_ifu',
        'buyer_identity_id',
        'buyer_institution_id',
        'reform_declaration_id',
        'custom_receipt_reference',
        'pickup_order_path',
        'divesting_file_path'
    ];

    protected $appends = [
        'pickup_order',
        'divesting_file'
    ];

    protected $casts = [
        'pickup_order_path' => 'array',
        'divesting_file_path' => 'array',
    ];

    static function relations()
    {
        return [
            'declaration',
            'reformDeclaration',
            'vehicle',
        ];
    }

    static function secureDeleteRelations(): array
    {
        return [
        ];
    }

    public function declaration()
    {
        return $this->belongsTo(AuctionSaleDeclaration::class, 'auction_sale_declaration_id');
    }

    public function reformDeclaration()
    {
        return $this->belongsTo(ReformDeclaration::class, 'reform_declaration_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function getFilters()
    {
        return [
            new Sort(),
            new ExactFilter('auction_sale_declaration_id'),
            new ExactFilter('vehicle_id'),
            new ExactFilter('buyer_npi'),
            new ExactFilter('buyer_ifu'),
            new ExactFilter('buyer_identity_id'),
            new ExactFilter('buyer_institution_id'),
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    public function getEntityName(): string
    {
        return 'Véhicule vendu aux enchères';
    }

    public function getPickupOrderAttribute()
    {
        if (isset($this->attributes['pickup_order_path'])) {
            return asset(json_decode($this->attributes['pickup_order_path'])->path);
        }
        return null;
    }

    public function getDivestingFileAttribute()
    {
        if (isset($this->attributes['divesting_file_path'])) {
            return asset(json_decode($this->attributes['divesting_file_path'])->path);
        }
        return null;
    }

    public function buyerIdentity()
    {
        return $this->belongsTo(Identity::class, 'buyer_identity_id');
    }

    public function buyerInstitution()
    {
        return $this->belongsTo(Institution::class, 'buyer_institution_id');
    }
}
