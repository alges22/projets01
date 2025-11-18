<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  Request  $request
     *
     */
    protected function redirectTo($request): JsonResponse|string|null
    {
        return  response()->json('Unauthenticated. Please provide a valid access token', ResponseAlias::HTTP_UNAUTHORIZED);
    }
}
