<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Profile\UpdateProfileRequest;
use App\Models\Auth\Profile;
use App\Repositories\Auth\ProfileRepository;


class ProfileController extends Controller
{
    public function __construct(private readonly ProfileRepository $repository)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response($this->repository->getAll());
    }

    /**
     * Display the specified resource.
     */
    public function show(Profile $profile)
    {
        return response($this->repository->view($profile));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProfileRequest $request, Profile $profile)
    {
        return response($this->repository->updateProfile($profile, $request->validated(),));
    }

}
