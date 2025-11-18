<?php

namespace App\Models\Auction;

use App\Models\Auth\Profile;
use App\Models\Institution\Institution;
use App\Models\Reform\ReformDeclaration;
use App\Services\IdentityService;
use App\Traits\HasCertificate;
use App\Traits\QrCodeTrait;
use App\Traits\SecureDelete;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\ExactFilter;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ntech\ActivityLogPackage\Traits\ActivityTrait;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Str;
use Ntech\UserPackage\Models\Identity;

class AuctionSaleDeclaration extends Model implements CanFilterContract
{
    use HasFactory, HasUuids, SoftDeletes, Filterable, ActivityTrait, LogsActivity, HasCertificate, SecureDelete, QrCodeTrait;

    protected $fillable = [
        'auctioneer_id',
        'institution_id',
        'report_path',
        'officials',
        'reference',
        'qr_code_path',
    ];

    protected $casts = [
        'report_path' => 'array',
        'officials' => 'array',
    ];

    protected $appends = [
        'report',
    ];

    public function auctioneer()
    {
        return $this->belongsTo(Profile::class, 'auctioneer_id');
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function getFilters()
    {
        return [
            new Sort(),
            new ExactFilter('institution_id'),
            new ScopeFilter('search'),
        ];
    }

    public function scopeSearch($query, string $keyword)
    {
        $keyword = strtolower($keyword);

        return $query->where(function ($query)  use ($keyword) {
            $query->whereRaw("LOWER(reference) like ?", ["%$keyword%"]);
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    public function getEntityName(): string
    {
        return 'Déclaration de vente aux enchères';
    }

    public function getReportAttribute()
    {
        if (isset($this->attributes['report_path'])) {
            return asset(json_decode($this->attributes['report_path'])->path);
        }
        return null;
    }

    public function saledVehicles()
    {
        return $this->hasMany(AuctionSaleVehicle::class);
    }

    static function relations()
    {
        return [
            'auctioneer:id,type_id,institution_id,identity_id,number',
            'auctioneer.type:id,code,name',
            'auctioneer.institution:id,name,telephone,email,ifu,rccm,address',
            'auctioneer.identity:id,firstname,lastname,email,telephone,ifu,npi',
            'institution:id,name,ifu,email,telephone,address',
            'saledVehicles:id,auction_sale_declaration_id,vehicle_id,price,buyer_ifu,buyer_npi',
            'saledVehicles.buyerIdentity:id,firstname,lastname,email,telephone,ifu,npi',
            'saledVehicles.buyerInstitution:id,name,telephone,email,ifu,rccm,address',
            'saledVehicles.vehicle:id,vin,vehicle_model,customs_reference,number_of_seats,engin_number,first_circulation_year',
        ];
    }

    static function secureDeleteRelations()
    {
        return [
            'saledVehicles',
        ];
    }

    public function generateReference()
    {
        return 'ASD-' . now()->timestamp . Str::random(3);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->reference = $model->generateReference();
            $model->qr_code_path = $model->generateQrCode($model->reference);
        });
    }

    public function getOfficialIdentitiesAttribute()
    {
        $officials = collect();

        foreach ($this->officials as $official) {
            $identity = Identity::where('npi', $official['npi'])->first();

            if ($identity) {
                $officials->push([
                    'id' => $identity->id,
                    'npi' => $official['npi'],
                    'title' => $official['title'],
                    'full_name' => $identity->full_name,
                ]);
            } else {
                $response = (new IdentityService)->showByNpi($official['npi'])->response()->getData(true)['data'];
                if (!isset($response['npi'])) {
                    $officials->push([
                        'id' => null,
                        'npi' => $official['npi'],
                        'title' => $official['title'],
                        'full_name' => '',
                    ]);
                } else {
                    $identity = $response->json();

                    $officials->push([
                        'id' => null,
                        'npi' => $response['npi'],
                        'title' => $official['title'],
                        'full_name' => $response['lastname'] . ' ' . $response['firstname'],
                    ]);
                }
            }
        }

        return $officials;
    }

    public function reformDeclarations()
    {
        return $this->hasMany(ReformDeclaration::class);
    }
}
