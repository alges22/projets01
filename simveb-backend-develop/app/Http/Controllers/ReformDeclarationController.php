<?php

namespace App\Http\Controllers;

use App\Http\Requests\Reform\ReformDeclarationRequest;
use App\Models\Reform\ReformDeclaration;
use App\Repositories\ReformDeclarationRepository;
use App\Traits\CrudRepositoryTrait;

class ReformDeclarationController extends Controller
{
    use CrudRepositoryTrait;

    public function __construct(private readonly ReformDeclarationRepository $reformDeclarationRepository)
    {
        $this->initRepository(ReformDeclaration::class);
        $this->authorizeResource(ReformDeclaration::class);
        $this->middleware('permission:update-profile-type')->only(['generateCertificate']);
    }

    public function index()
    {
        return response($this->repository->getAll(relations: ReformDeclaration::relations()));
    }

    public function store(ReformDeclarationRequest $request)
    {
        return response($this->reformDeclarationRepository->store($request->validated()));
    }

    public function show(ReformDeclaration $reformDeclaration)
    {
        return response($reformDeclaration->load(ReformDeclaration::relations()));
    }

    public function generateCertificate(ReformDeclaration $reformDeclaration)
    {
        return response($reformDeclaration->generateCertificate(stream: true, view: $reformDeclaration->certificateView()));
    }

    public function edit(ReformDeclaration $reformDeclaration)
    {
        return response([
            'reform_declaration' => $reformDeclaration,
        ]);
    }

    public function update(ReformDeclarationRequest $request, ReformDeclaration $reformDeclaration)
    {
        return response($this->reformDeclarationRepository->update($reformDeclaration, $request->validated()));
    }

    public function destroy( ReformDeclaration $reformDeclaration)
    {
        return response($this->repository->destroy($reformDeclaration));
    }
}
