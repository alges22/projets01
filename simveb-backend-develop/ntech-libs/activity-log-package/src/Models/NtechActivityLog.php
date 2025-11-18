<?php

namespace Ntech\ActivityLogPackage\Models;

use App\Consts\Utils;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;

class NtechActivityLog extends Activity implements CanFilterContract
{
    use HasUuids, Filterable;

    public function getFilters(): array
    {
        return [
            new ScopeFilter('search'),
            new Sort()
        ];
    }

    public function scopeSearch(Builder $query, string $keyword)
    {
        return $query->where(function (Builder $query)  use ($keyword) {
            $query
                ->where('event', 'like', "%{$keyword}%")
                ->orWhere('log_action', 'like', "%{$keyword}%")
                ->orWhere('description', 'like', "%{$keyword}%")
            ;
        });
    }
    protected function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => Carbon::parse($value)->translatedFormat(Utils::COMMON_DATE_FORMAT)
        );
    }
    protected function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => $value ? Carbon::parse($value)->translatedFormat(Utils::COMMON_DATE_FORMAT) : $value
        );
    }
    protected function deletedAt(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => $value ? Carbon::parse($value)->translatedFormat(Utils::COMMON_DATE_FORMAT) : $value
        );
    }
}
