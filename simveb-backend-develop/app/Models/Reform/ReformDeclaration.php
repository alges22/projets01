<?php

namespace App\Models\Reform;

use App\Models\Auction\AuctionSaleDeclaration;
use App\Models\Auction\AuctionSaleVehicle;
use App\Models\Auth\Profile;
use App\Traits\HasCertificate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\SecureDelete;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\ExactFilter;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ntech\ActivityLogPackage\Traits\ActivityTrait;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Str;

class ReformDeclaration extends Model implements CanFilterContract
{
    use HasFactory, HasUuids, SoftDeletes, Filterable, ActivityTrait, LogsActivity, HasCertificate, SecureDelete, Filterable;

    protected $fillable = [
        'declarant_id',
        'auction_sale_declaration_id',
        'report_path',
        'reference',
    ];

    protected $casts = [
        'report_path' => 'array',
    ];

    protected $appends = [
        'report',
    ];

    public function declarant()
    {
        return $this->belongsTo(Profile::class, 'declarant_id');
    }

    public function auctionSaleDeclaration()
    {
        return $this->belongsTo(AuctionSaleDeclaration::class);
    }

    public function getFilters()
    {
        return [
            new ScopeFilter('search'),
            new Sort(),
            new ExactFilter('auction_sale_declaration_id'),
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    public function getEntityName(): string
    {
        return 'Déclaration des réformes';
    }

    public function scopeSearch($query, string $keyword)
    {
        return $query->where(function ($query)  use ($keyword) {
            $query->where('reference', 'like', "%$keyword%");
        });
    }

    public function getReportAttribute()
    {
        if (isset($this->attributes['report_path'])) {
            return asset(json_decode($this->attributes['report_path'])->path);
        }
        return null;
    }

    public function reformedVehicles()
    {
        return $this->hasMany(AuctionSaleVehicle::class);
    }

    static function relations()
    {
        return [
            'declarant:id,type_id,institution_id,identity_id,number',
            'declarant.type:id,code,name',
            'declarant.institution:id,name,telephone,email,ifu,rccm,address',
            'declarant.identity:id,firstname,lastname,email,telephone,ifu,npi',
            'auctionSaleDeclaration:id,auctioneer_id,qr_code_path,institution_id,report_path,officials,reference',
            'reformedVehicles',
            'reformedVehicles.buyerIdentity:id,firstname,lastname,email,telephone,ifu,npi',
            'reformedVehicles.buyerInstitution:id,name,telephone,email,ifu,rccm,address',
            'reformedVehicles.vehicle',
        ];
    }

    static function secureDeleteRelations()
    {
        return [
            'reformedVehicles'
        ];
    }

    public function generateReference()
    {
        return 'RD-' . now()->timestamp . Str::random(3);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->reference = $model->generateReference();
        });
    }
}
