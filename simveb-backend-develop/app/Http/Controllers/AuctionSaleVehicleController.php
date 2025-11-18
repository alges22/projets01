<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auction\AuctionSaleVehicleRequest;
use App\Models\Auction\AuctionSaleVehicle;
use App\Repositories\AuctionSaleVehicleRepository;
use App\Traits\CrudRepositoryTrait;

class AuctionSaleVehicleController extends Controller
{
    use CrudRepositoryTrait;

    public function __construct(private readonly AuctionSaleVehicleRepository $auctionSaleVehicleRepository)
    {
        $this->initRepository(AuctionSaleVehicle::class);
        $this->authorizeResource(AuctionSaleVehicle::class);
    }

    public function show(AuctionSaleVehicle $auctionSaleVehicle)
    {
        return response($auctionSaleVehicle->load(AuctionSaleVehicle::relations()));
    }

    public function update(AuctionSaleVehicleRequest $request, AuctionSaleVehicle $auctionSaleVehicle)
    {
        return response($this->auctionSaleVehicleRepository->update($auctionSaleVehicle, $request->validated()));
    }

    public function destroy( AuctionSaleVehicle $auctionSaleVehicle)
    {
        return response($this->repository->destroy($auctionSaleVehicle));
    }
}
