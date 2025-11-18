<?php

namespace App\Http\Controllers\Client\Service;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClientServiceResource;
use App\Repositories\ServiceRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;

class ServiceController extends Controller
{

    public function __construct(private ServiceRepository $repository)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response(ClientServiceResource::collection($this->repository->getActive()));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $serviceCode, ServiceRepository $repository): \Illuminate\Foundation\Application|Response|Application|ResponseFactory
    {
        return response($repository->model
            ->newQuery()
            ->whereHas('type', fn($query) => $query->where('code', $serviceCode))
            ->with(['children' => function ($query) {
                $query->where('is_active', true)
                    ->where('can_be_demanded', true)
                    ->orderByDesc('created_at');
            }])
            ->firstOrFail());
    }

}
