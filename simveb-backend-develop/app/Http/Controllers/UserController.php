<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Ntech\UserPackage\Services\UserService;

class UserController extends Controller
{
    public function __construct(private readonly UserService $userService)
    {
    }

    public function show($npi)
    {
        return $this->userService->getUserDetails($npi);
    }
}
