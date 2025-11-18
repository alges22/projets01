<?php

namespace App\Models;

use App\Interfaces\ModelHasRelations;
use App\Models\Order\Demand;
use App\Traits\HasFiles;
use App\Traits\HasStatusLabel;
use App\Traits\HasTransactions;
use App\Traits\HasTreatments;
use Baro\PipelineQueryCollection\Concerns\Filterable;
use Baro\PipelineQueryCollection\Contracts\CanFilterContract;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ntech\RequiredDocumentPackage\Traits\HasRequiredDocumentTypes;

class GrayCardDuplicate extends Model implements CanFilterContract, ModelHasRelations
{
    use HasFactory,
        HasUuids,
        HasRequiredDocumentTypes,
        Filterable,
        HasTreatments,
        HasTransactions,
        HasFiles,
        HasStatusLabel;

    protected $fillable = [
        'reference',
        'reason',
        'new_gray_card_id',
        'old_gray_card_id',
        'demand_id',
        'issued_at',
    ];

    const IS_LOST = 'IS_LOST';
    const IS_SPOILED = 'IS_SPOILED';

    static function relations(): array
    {
        return [];
    }

    static function secureDeleteRelations(): array
    {
        return [];
    }

    public function getEntityName(): string
    {
        return "Duplicata de carte grise";
    }

    public function demand()
    {
        return $this->belongsTo(Demand::class);
    }

    public function oldGrayCard()
    {
        return $this->belongsTo(GrayCard::class, 'old_gray_card_id');
    }

    public function newGrayCard()
    {
        return $this->belongsTo(GrayCard::class, 'new_gray_card_id');
    }

}
