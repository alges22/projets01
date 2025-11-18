<?php

namespace App\Http\Controllers\Immatriculation;

use App\Http\Controllers\Controller;
use App\Models\Immatriculation\Immatriculation;
use App\Traits\CrudRepositoryTrait;
use Illuminate\Http\Request;

class ImmatriculationController extends Controller
{
    use CrudRepositoryTrait;

    public function __construct()
    {
        $this->initRepository(Immatriculation::class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response($this->repository->getAll(relations: ['vehicle.owner']));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Immatriculation $immatriculation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Immatriculation $immatriculation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Immatriculation $immatriculation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Immatriculation $immatriculation)
    {
        //
    }
}
