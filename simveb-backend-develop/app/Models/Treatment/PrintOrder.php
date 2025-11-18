<?php

namespace App\Models\Treatment;

use App\Consts\AvailableServiceType;
use App\Enums\PrintOrderTypesEnum;
use App\Enums\Status;
use App\Models\Auth\Profile;
use App\Models\Immatriculation\Immatriculation;
use App\Models\Institution\Institution;
use App\Models\Order\Demand;
use App\Models\Plate;
use App\Models\Vehicle\VehicleCharacteristicCategory;
use App\Traits\HasDemandOtps;
use App\Traits\HasImages;
use App\Traits\HasStatusLabel;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\ExactFilter;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PrintOrder extends Model implements CanFilterContract
{
    use HasFactory, Filterable, HasUuids, HasStatusLabel, SoftDeletes, HasDemandOtps, HasImages;

    protected $fillable = [
        'demand_id',
        'reference',
        'type',
        'author_id',
        'status',
        'validated_at',
        'rejected_at',

        // plate attributes
        'plate_status', // nullable
        'institution_id',
        'plate_affected_at',
        'plate_printer_id',
        'plate_printed_at',
        'plate_validator_id',
        'plate_validated_at',
        'plate_rejector_id',
        'plate_rejected_at',
        'plate_observations',

        // card attributes
        'card_status', // nullable
        'card_printer_id',
        'card_printed_at',
        'card_validator_id',
        'card_validated_at',
        'card_rejector_id',
        'card_rejected_at',
        'card_observations',
    ];

    protected $appends = [
        'type_label',
        'plate_status_label',
        'card_status_label',
    ];

    public function getFilters()
    {
        return [
            new Sort,
            new ScopeFilter('search'),
            new ExactFilter('institution_id'),
            new ExactFilter('author_id'),
            new ExactFilter('plate_printer_id'),
            new ExactFilter('plate_validator_id'),
            new ExactFilter('plate_rejector_id'),
            new ExactFilter('card_printer_id'),
            new ExactFilter('card_validator_id'),
            new ExactFilter('card_rejector_id'),
            new ExactFilter('type'),
        ];
    }

    static function relations()
    {
        return [
            'institution:id,name,ifu,logo_path',
            'platePrinter.identity:id,firstname,lastname,npi,telephone',
            'plateValidator.identity:id,firstname,lastname,npi,telephone',
            'plateRejector.identity:id,firstname,lastname,npi,telephone',
            'cardPrinter.identity:id,firstname,lastname,npi,telephone',
            'cardValidator.identity:id,firstname,lastname,npi,telephone',
            'cardRejector.identity:id,firstname,lastname,npi,telephone',
            'demand.service',
            'demand.model',
            'images',
            'plates',
        ];
    }

    public function getEntityName()
    {
        return "Ordre d'impression";
    }

    public function demand()
    {
        return $this->belongsTo(Demand::class);
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function author()
    {
        return $this->belongsTo(Profile::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->reference = 'PO-' . now()->timestamp . strtoupper(Str::random(3));
        });
    }

    public function platePrinter()
    {
        return $this->belongsTo(Profile::class, 'plate_printer_id');
    }

    public function plateValidator()
    {
        return $this->belongsTo(Profile::class, 'plate_validator_id');
    }

    public function plateRejector()
    {
        return $this->belongsTo(Profile::class, 'plate_rejector_id');
    }

    public function cardPrinter()
    {
        return $this->belongsTo(Profile::class, 'card_printer_id');
    }

    public function cardValidator()
    {
        return $this->belongsTo(Profile::class, 'card_validator_id');
    }

    public function cardRejector()
    {
        return $this->belongsTo(Profile::class, 'card_rejector_id');
    }

    public function scopeSearch($query, string $keyword)
    {
        $keyword = strtolower($keyword);

        return $query->where(function ($query)  use ($keyword) {
            return $query->where('reference', 'like', "%$keyword%");
        });
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function plates()
    {
        return $this->belongsToMany(Plate::class)->withPivot(['side']);
    }

    public function getTypeLabelAttribute()
    {
        return getModelAttributeLabelFromEnum($this, 'type', PrintOrderTypesEnum::class);
    }

    public function getCardStatusLabelAttribute()
    {
        return getModelAttributeLabelFromEnum($this, 'card_status', Status::class);
    }

    public function getPlateStatusLabelAttribute()
    {
        return getModelAttributeLabelFromEnum($this, 'plate_status', Status::class);
    }

    public function getImmatriculationAttribute()
    {
        $serviceCode = $this->demand->service->type->code;

        $immatriculation = match ($serviceCode) {
            AvailableServiceType::GRAY_CARD_DUPLICATE => $this->demand->model->oldGrayCard->immatriculation,
            AvailableServiceType::VEHICLE_TRANSFORMATION => $this->demand->model->vehicle->frontPlate ? $this->demand->model->vehicle->frontPlate->immatriculation : $this->demand->model->vehicle->backPlate->immatriculation,
            AvailableServiceType::IMMATRICULATION_STANDARD => $this->demand->model,
            default => $this->demand->model->immatriculation,
        };

        if ($immatriculation && $immatriculation instanceof Immatriculation) {
            $immatriculation->load([
                'frontPlateShape:id,name,code',
                'backPlateShape:id,name,code',
                'plateColor:id,name,label,color_code,text_color',
                'vehicle',
                'vehicle.owner.identity:id,firstname,lastname,npi,ifu',
            ]);

            foreach (VehicleCharacteristicCategory::whereNotNull('field_name')->get() as $category) {
                $characteristics[] = [$category->label => $immatriculation->vehicle->characteristics()->where('category_id', $category->id)->first()?->value ?? null];
            }

            $immatriculation->vehicle->characteristics = $characteristics;

            return $immatriculation;
        }

        return null;
    }
}
