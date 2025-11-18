<?php

namespace App\Http\Controllers\Immatriculation;

use App\Http\Controllers\Controller;
use App\Models\Config\Town;
use App\Models\Immatriculation\ImmatriculationFormat;
use App\Services\Immatriculation\ImmatriculationNumberService;

class ImmatriculationNumberController extends Controller
{

    public function __construct(private readonly ImmatriculationNumberService $service) {}

    /**
     * Display a listing of the resource.
     */
    public function generateNumber()
    {
        $town = Town::query()->where('name', 'COTONOU')->first();

        return response($this->service->generateNewNumber(ImmatriculationFormat::first(), $town));
    }
}
