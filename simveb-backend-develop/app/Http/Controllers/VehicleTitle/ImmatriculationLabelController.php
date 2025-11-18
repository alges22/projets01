<?php

namespace App\Http\Controllers\VehicleTitle;

use App\Http\Controllers\Controller;
use App\Models\Immatriculation\ImmatriculationLabel;
use App\Traits\CrudRepositoryTrait;

class ImmatriculationLabelController extends Controller
{
    use CrudRepositoryTrait;
    public function __construct()
    {
        $this->initRepository(ImmatriculationLabel::class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response($this->repository->getAll(true, ImmatriculationLabel::relations()));
    }
}
