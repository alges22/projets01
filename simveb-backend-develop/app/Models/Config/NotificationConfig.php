<?php

namespace App\Models\Config;

use App\Traits\SecureDelete;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Baro\PipelineQueryCollection\ScopeFilter;
use Baro\PipelineQueryCollection\Sort;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;

class NotificationConfig extends Model implements CanFilterContract
{
    use HasFactory,
    Filterable,
    HasUuids,
    SecureDelete,
    SoftDeletes;

    protected $fillable = [
        'title',
        'message_sms',
        'message_in_app',
        'message_mail',
        'total_repetition_count',
        'frequency_in_days'
    ];

    protected $casts = [];

    protected $guarded = ['name'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    public function getFilters(): array
    {
        return [
            new ScopeFilter('search'),
            new Sort(),
        ];
    }

    public function scopeSearch(Builder $query, string $keyword)
    {
        return $query->where(function (Builder $query) use ($keyword) {
            $query->where('name', 'like', "%$keyword%")
                ->orWhere('title', 'like', "%$keyword%")
                ->orWhere('message_sms', 'like', "%$keyword%")
                ->orWhere('message_in_app', 'like', "%$keyword%")
                ->orWhere('message_mail', 'like', "%$keyword%")
            ;
        });
    }

    private function getEntityName() : string
    {
        return "Config des notifications";
    }

    public static function relations()
    {
        return [

        ];
    }

    public static function secureDeleteRelations()
    {
        return [

        ];
    }
}
