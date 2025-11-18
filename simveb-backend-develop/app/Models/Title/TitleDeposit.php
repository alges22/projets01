<?php

namespace App\Models\Title;

use App\Models\Config\TitleReason;
use App\Models\Order\Demand;
use App\Models\Vehicle\Vehicle;
use App\Models\Vehicle\VehicleOwner;
use App\Services\GeneratePdfService;
use App\Traits\HasCertificate;
use App\Traits\HasFiles;
use App\Traits\SecureDelete;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Response;
use Ntech\RequiredDocumentPackage\Traits\HasRequiredDocumentTypes;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Str;

class TitleDeposit extends Model implements CanFilterContract
{
    use HasUuids,
        HasFactory,
        HasRequiredDocumentTypes,
        HasFiles,
        Filterable,
        SecureDelete,
        SoftDeletes;
    use HasCertificate {
        generateCertificate as private myGenerateCertificate;
    }

    protected $fillable = [
        'reference',
        'demand_id',
        'vehicle_id',
        'vehicle_owner_id',
        'title_reason_id',
        'status',
    ];

    /**
     * @return string
     */
    public function getEntityName(): string
    {
        return "Dépot de titre";
    }

    /**
     * @return LogOptions
     */
    public function getActivityLogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    /**
     * @return array
     */
    public static function relations(): array
    {
        return [
            'demand',
            'vehicle',
            'owner',
            'reason',
            'certificate',
        ];
    }

    /**
     * @return array
     */
    public static function secureDeleteRelations(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return [
            new ScopeFilter('search'),
            new Sort(),
        ];
    }

    public function scopeSearch(Builder $query, string $keyword)
    {
        return $query->where(function (Builder $query)  use ($keyword) {
            $query->where('reference', 'like', "%$keyword%")
                ->orWhereRelation('vehicle', 'vin', 'like', "%$keyword%");
        });
    }

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->reference = generateReference('DT');
        });
    }

    /**
     * Get the demand that owns the TitleDeposit
     *
     * @return BelongsTo
     */
    public function demand(): BelongsTo
    {
        return $this->belongsTo(Demand::class);
    }

    /**
     * Get the vehicle that owns the TitleDeposit
     *
     * @return BelongsTo
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the owner that owns the TitleDeposit
     *
     * @return BelongsTo
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(VehicleOwner::class, 'vehicle_owner_id');
    }

    /**
     * Get the reason that owns the TitleDeposit
     *
     * @return BelongsTo
     */
    public function reason(): BelongsTo
    {
        return $this->belongsTo(TitleReason::class, 'title_reason_id');
    }


    /**
     *
     */
    public function generateCertificate(bool $stream = false, ?string $filename = null)
    {
        $filename = $filename ?:
            Str::snake(class_basename($this)) . '_certificate_' . now()->timestamp . '_' . Str::random(3) . '.pdf';

        $output = GeneratePdfService::generatePDF(
            view: 'certificates.title-deposit-certificate',
            data: [
                'titleDeposit' => $this->load(TitleDeposit::relations())
            ],
            filename: $filename,
            folder: 'certificates/' . Str::snake(class_basename($this)) . '_certificate',
            stream: $stream
        );

        return $stream ? $output : asset('storage/' . Str::after($output, 'public/storage/'));
    }
}
