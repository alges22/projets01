<?php

namespace App\Http\Controllers\Space;

use App\Http\Controllers\Controller;
use App\Http\Requests\Space\SpaceRequest;
use App\Models\Space\Space;
use App\Repositories\Space\SpaceRepository;
use App\Traits\CrudRepositoryTrait;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\Response;

class SpaceController extends Controller
{
    use CrudRepositoryTrait;

    public function __construct(private readonly SpaceRepository $spaceRepository)
    {
        $this->initRepository(Space::class);
        $this->authorizeResource(Space::class, 'space');
        $this->middleware('permission:browse-space-staff')->only(['members']);
        $this->middleware('permission:show-space')->only(['details']);
    }

    /**
     * @return Response|ResponseFactory
     */
    public function index()
    {
        return response($this->repository->getAll(true, ['profileType:id,code,name', 'institution:id,name',]));
    }

    /**
     * @return Response|ResponseFactory
     */
    public function show(Space $space)
    {
        return response($space->load([
            'institution:id,name',
            'profileType:id,code,name',
            'profiles:id,space_id,user_id',
            'profiles.user:id,username,email,identity_id',
            'profiles.user.identity',
            'files',
            'suspensionRequests.author.identity:id,firstname,lastname', 'suspensionRequests.validator.identity:id,firstname,lastname','suspensionRequests.rejector.identity:id,firstname,lastname',
            'suspensionLiftingRequests.author.identity:id,firstname,lastname', 'suspensionLiftingRequests.validator.identity:id,firstname,lastname','suspensionLiftingRequests.rejector.identity:id,firstname,lastname',
        ]));
    }

    /**
     * @return Response|ResponseFactory
     */
    public function edit(Space $space)
    {
        return response(array_merge(
            ['space' => $space],
            $this->spaceRepository->edit()
        ));
    }

    /**
     * @param SpaceRequest $request
     * @param Space $space
     * @return \Illuminate\Contracts\Foundation\Application|ResponseFactory|Application|Response
     */
    public function update(SpaceRequest $request, Space $space)
    {
        return response($this->spaceRepository->update($space, $request->validated()));
    }

    /**
     * @param Space $space
     * @return Response|ResponseFactory
     */
    public function destroy(Space $space)
    {
        return response($this->repository->destroy($space));
    }

    public function members()
    {
        return $this->spaceRepository->members();
    }

    /**
     *
     */
    public function details()
    {
        return response($this->spaceRepository->details());
    }
}
