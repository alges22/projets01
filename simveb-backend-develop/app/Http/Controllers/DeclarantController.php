<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeclarantRequest;
use App\Models\Account\Declarant;
use App\Repositories\DeclarantRepository;
use App\Traits\CrudRepositoryTrait;

class DeclarantController extends Controller
{

    use CrudRepositoryTrait;

    public function __construct(private readonly DeclarantRepository $declarantRepository)
    {
        $this->authorizeResource(Declarant::class);
    }

    public function index()
    {
        return response($this->declarantRepository->getAll());
    }

    public function store(DeclarantRequest $request, DeclarantRepository $declarantRepository)
    {
        return response($declarantRepository->createDeclarant($request->validated()));
    }

    public function show($declarantId)
    {
        $declarant = $this->declarantRepository->showDeclarant($declarantId);

        return response($declarant);
    }

    public function update(DeclarantRequest $request, Declarant $declarant, DeclarantRepository $declarantRepository)
    {
        return response($declarantRepository->updateDeclarant($request->validated(), $declarant));
    }

    public function destroy(Declarant $declarant)
    {
        $declarant = $this->declarantRepository->deleteDeclarant($declarant);

        return response($declarant);
    }
}
