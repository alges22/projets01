<?php

namespace App\Repositories;

use App\Models\Config\Border;
use App\Models\Config\Country;
use App\Models\Config\Town;
use App\Repositories\Crud\AbstractCrudRepository;

class BorderRepository extends AbstractCrudRepository
{
    public function __construct()
    {
        parent::__construct(Border::class);
    }

    public function create()
    {
        return [
            'countries' => Country::countries()->select(['id', 'name'])->get(),
            'towns' => Town::query()->select(['id', 'name'])->get(),
        ];
    }
}
