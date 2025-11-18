<?php

namespace App\Http\Controllers\Admin\Config;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Response;
use App\Http\Controllers\ResponseFactory;
use App\Http\Requests\BlacklistPerson\ImportBlacklistPersonRequest;
use App\Http\Requests\BlacklistPerson\BlacklistPersonRequest;
use App\Models\Config\BlacklistPerson;
use App\Repositories\BlacklistPersonRepository;
use App\Traits\CrudRepositoryTrait;

class BlacklistPersonController extends Controller
{
    use CrudRepositoryTrait;

    public function __construct(private readonly BlacklistPersonRepository $blacklistPersonrepository)
    {
        $this->initRepository(BlacklistPerson::class);
        $this->authorizeResource(BlacklistPerson::class);
        $this->middleware('permission:store-blacklist-person')->only(['fileFormat', 'import']);
    }

    /**
     * @return Response|ResponseFactory
     */
    public function index()
    {
        return response($this->repository->getAll());
    }

    /**
     * @param BlacklistPersonRequest $request
     * @return Response|ResponseFactory
     */
    public function store(BlacklistPersonRequest $request)
    {
        return response($this->repository->store($request->validated()));
    }

    /**
     * @param BlacklistPerson $blacklistPerson
     * @return Response|ResponseFactory
     */
    public function show(BlacklistPerson $blacklistPerson)
    {
        return response($blacklistPerson->load(BlacklistPerson::relations()));
    }

    /**
     * @param BlacklistPersonRequest $request
     * @param BlacklistPerson $blacklistPerson
     * @return Response|ResponseFactory
     */
    public function update(BlacklistPersonRequest $request, BlacklistPerson $blacklistPerson)
    {
        return response($this->repository->update($blacklistPerson, $request->validated()));
    }

    /**
     * @param BlacklistPerson $blacklistPerson
     * @return Response|ResponseFactory
     */
    public function destroy(BlacklistPerson $blacklistPerson)
    {
        return response($this->repository->destroy($blacklistPerson));
    }

    public function fileFormat()
    {
        $path = 'format-import/blacklist_persons.xlsx';

        return response(file_exists(public_path($path)) ? asset($path) : "");
    }

    public function import(ImportBlacklistPersonRequest $request)
    {
        return response($this->blacklistPersonrepository->import($request->validated()));
    }
}
