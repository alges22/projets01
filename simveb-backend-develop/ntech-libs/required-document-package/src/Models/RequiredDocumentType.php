<?php

namespace Ntech\RequiredDocumentPackage\Models;

use App\Models\Space\Space;
use App\Models\Space\SpaceRegistrationRequest;
use App\Models\Config\IntlVehicleRegDoc;
use App\Models\Config\Service;
use App\Models\Order\Demand;
use App\Models\PlateTransformation;
use App\Models\PrestigeLabelImmatriculation;
use App\Traits\SecureDelete;
use Baro\PipelineQueryCollection\BooleanFilter;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\ExactFilter;
use Baro\PipelineQueryCollection\RelativeFilter;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ntech\ActivityLogPackage\Traits\ActivityTrait;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class RequiredDocumentType extends Model implements CanFilterContract
{
    use HasFactory, HasUuids, ActivityTrait, LogsActivity, SecureDelete, Filterable, SoftDeletes;

    protected $fillable = [
        'document_type_id',
        'relation_type',
        'required',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    public function getFilters(): array
    {
        return [
            new ScopeFilter('search'),
            new Sort(),
            new ExactFilter('document_type_id'),
            new RelativeFilter('relation_type'),
            new BooleanFilter('required'),
        ];
    }

    public function scopeSearch(Builder $query, string $keyword)
    {
        return $query->where(function (Builder $query)  use ($keyword) {
            $query->where('relation_type', "%$keyword%")
                ->orWhereHas('documentType', function ($q) use ($keyword) {
                    $q->where('code', 'like', "%$keyword%")
                        ->orWhere('description', 'like', "%$keyword%");
                });
        });
    }

    private function getEntityName(): string
    {
        return "Document requis";
    }

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }

    static function relations()
    {
        return [
            'documentType:id,code,description',
        ];
    }

    static function secureDeleteRelations()
    {
        return [
            //
        ];
    }

    static function types(): array
    {
        return [
            SpaceRegistrationRequest::class,
            Space::class,
            Service::class,
            Demand::class,
            IntlVehicleRegDoc::class,
            PrestigeLabelImmatriculation::class,
            PlateTransformation::class
        ];
    }
}
