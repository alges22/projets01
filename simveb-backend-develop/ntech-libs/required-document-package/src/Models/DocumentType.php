<?php

namespace Ntech\RequiredDocumentPackage\Models;

use App\Traits\SecureDelete;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
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

class DocumentType extends Model implements CanFilterContract
{
    use HasFactory, HasUuids, ActivityTrait, LogsActivity, SecureDelete, Filterable, SoftDeletes;

    protected $fillable = [
        'code',
        'description',
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
            new RelativeFilter('code'),
            new RelativeFilter('description'),
        ];
    }

    public function scopeSearch(Builder $query, string $keyword)
    {
        return $query->where(function (Builder $query)  use ($keyword) {
            $query->where('code', 'like', "%$keyword%")
                ->orWhere('description', 'like', "%$keyword%");
        });
    }

    private function getEntityName(): string
    {
        return "Type de document";
    }

    public function documents()
    {
        return $this->belongsToMany(DocumentType::class);
    }

    static function relations()
    {
        return [
            //
        ];
    }

    static function secureDeleteRelations()
    {
        return [
            'requiredDocumentTypes',
        ];
    }
}
