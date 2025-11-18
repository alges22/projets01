<?php

namespace App\Http\Middleware;

use App\Enums\Status;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SpaceAcessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user?->onlineProfile?->space?->status == Status::suspended->name) {
            return response(['message' => "L'espace auquel vous êtes connecté est suspendu, veuillez changer d'espace"], Response::HTTP_FORBIDDEN);
        }

        if ($user?->onlineProfile?->suspended_at !== null) {
            return response(['message' => "Votre profile est suspendu! Veuillez contactez l'espace"], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
