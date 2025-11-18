<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImpressionDemand\ConfirmImpressionDemandRequest;
use App\Http\Requests\ImpressionDemand\ImpressionDemandRequest;
use App\Http\Requests\ImpressionDemand\InitImpressionDemandRequest;
use App\Http\Requests\ImpressionDemand\RejectImpressionDemandRequest;
use App\Http\Requests\ImpressionDemand\ValidateImpressionDemandRequest;
use App\Models\ImpressionDemand;
use App\Repositories\ImpressionDemandRepository;

class ImpressionDemandController extends Controller
{
    public function __construct(private readonly ImpressionDemandRepository $impressionDemandRepository)
    {
        $this->authorizeResource(ImpressionDemand::class);
    }

    public function initDemand(InitImpressionDemandRequest $request)
    {
        if (!auth()->user()->hasPermissionTo('store-impression-demand')) {
            return response([
                "success" => false,
                "message" =>  "Cet utilisateur n'a pas la permission d'accéder à cette ressource."
            ], 401);
        }
        [$success, $result] = $this->impressionDemandRepository->initDemand($request->validated());

        return response($result, $success ? 200 : 422);
    }

    public function store(ImpressionDemandRequest $request)
    {
        [$success, $result] = $this->impressionDemandRepository->store($request->validated());

        return response($result, $success ? 200 : 422);
    }

    public function index()
    {
        return response($this->impressionDemandRepository->getAll(true, ImpressionDemand::relations()));
    }

    public function show(ImpressionDemand $impressionDemand)
    {
        return response($impressionDemand->load(ImpressionDemand::relations()));
    }

    public function validationCreate(ImpressionDemand $impressionDemand)
    {
        if (!auth()->user()->hasPermissionTo('validate-impression-demand')) {
            return response([
                "success" => false,
                "message" =>  "Cet utilisateur n'a pas la permission d'accéder à cette ressource."
            ], 401);
        }
        return response($this->impressionDemandRepository->validationCreate($impressionDemand));
    }

    public function validateDemand(ValidateImpressionDemandRequest $request)
    {
        if (!auth()->user()->hasPermissionTo('validate-impression-demand')) {
            return response([
                "success" => false,
                "message" =>  "Cet utilisateur n'a pas la permission d'accéder à cette ressource."
            ], 401);
        }
        [$success, $result] = $this->impressionDemandRepository->validateDemand($request->validated());

        return response($result, $success ? 200 : 422);
    }

    public function rejectDemand(RejectImpressionDemandRequest $request)
    {
        if (!auth()->user()->hasPermissionTo('reject-impression-demand')) {
            return response([
                "success" => false,
                "message" =>  "Cet utilisateur n'a pas la permission d'accéder à cette ressource."
            ], 401);
        }
        [$success, $result] = $this->impressionDemandRepository->rejectDemand($request->validated());

        return response($result, $success ? 200 : 422);
    }

    public function confirmDemand(ConfirmImpressionDemandRequest $request)
    {
        if (!auth()->user()->hasPermissionTo('confirm-impression-demand')) {
            return response([
                "success" => false,
                "message" =>  "Cet utilisateur n'a pas la permission d'accéder à cette ressource."
            ], 401);
        }
        [$success, $result] = $this->impressionDemandRepository->confirmDemand($request->validated());

        return response($result, $success ? 200 : 422);
    }

    public function confirmPlateReception(ImpressionDemand $impressionDemand)
    {
        if (!auth()->user()->hasPermissionTo('confirm-plate-reception')) {
            return response([
                "success" => false,
                "message" =>  "Cet utilisateur n'a pas la permission d'accéder à cette ressource."
            ], 401);
        }

        [$success, $result] = $this->impressionDemandRepository->confirmPlateReception($impressionDemand);

        return response($result, $success ? 200 : 422);
    }
}
