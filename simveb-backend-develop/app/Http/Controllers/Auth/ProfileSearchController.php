<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Profile\ProfileSearchRequest;
use App\Models\Auth\Profile;
use App\Repositories\Auth\ProfileSearchRepository;

class ProfileSearchController extends Controller
{
    public function __construct(private readonly ProfileSearchRepository $repository)
    {
    }

    /**
     * Handle the incoming request.
     */
    public function __invoke(ProfileSearchRequest $request)
    {
        // $this->authorize();
        return response($this->repository->search($request->validated()));
    }

    /**
     * Display the specified resource.
     */
    public function show(Profile $profile)
    {
        return response($this->repository->getProfileDemands($profile));
    }
}
