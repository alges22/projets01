<?php

namespace App\Repositories;

use App\Models\Config\TitleReason;
use App\Models\Config\TitleReasonType;
use App\Repositories\Crud\AbstractCrudRepository;

class TitleReasonRepository extends AbstractCrudRepository
{
    public function __construct()
    {
      parent::__construct(TitleReason::class);
    }

    public function create()
    {
        return [
            'title_reasons' => TitleReasonType::query()->select(['id', 'name' ,'description'])->get(),
        ];
    }
}
