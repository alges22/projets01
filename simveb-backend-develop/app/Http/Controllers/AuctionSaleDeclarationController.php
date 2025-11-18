<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auction\AddOfficialInAuctionSaleDeclarationRequest;
use App\Http\Requests\Auction\AddVehicleInAuctionSaleDeclarationRequest;
use App\Http\Requests\Auction\AuctionSaleDeclarationRequest;
use App\Http\Requests\Auction\RemoveOfficialFromAuctionSaleDeclarationRequest;
use App\Http\Requests\Auction\RemoveVehicleFromAuctionSaleDeclarationRequest;
use App\Models\Auction\AuctionSaleDeclaration;
use App\Models\Institution\Institution;
use App\Repositories\AuctionSaleDeclarationRepository;
use App\Traits\CrudRepositoryTrait;

class AuctionSaleDeclarationController extends Controller
{
    use CrudRepositoryTrait;

    public function __construct(private readonly AuctionSaleDeclarationRepository $auctionSaleDeclarationRepository)
    {
        $this->initRepository(AuctionSaleDeclaration::class);
        $this->authorizeResource(AuctionSaleDeclaration::class);
        $this->middleware('permission:show-auction-sale-declaration')->only(['generateCertificate']);
        $this->middleware('permission:update-auction-sale-declaration')->only(['addVehicle', 'addOfficial', 'removeVehicle','removeOfficial']);
    }

    public function index()
    {
        return response($this->repository->getAll(relations: AuctionSaleDeclaration::relations()));
    }

    public function create()
    {
        return response([
            'institutions' => Institution::select(['id', 'name'])->orderBy('name', 'asc')->get(),
        ]);
    }

    public function store(AuctionSaleDeclarationRequest $request)
    {
        return response($this->auctionSaleDeclarationRepository->store($request->validated()));
    }

    public function showByReference(string $reference)
    {
        [$success, $result] = $this->auctionSaleDeclarationRepository->showByReference($reference);

        return response($result, $success ? 200 : 404);
    }

    public function show(AuctionSaleDeclaration $auctionSaleDeclaration)
    {
        return response($auctionSaleDeclaration->load(AuctionSaleDeclaration::relations())->append('official_identities'));
    }

    public function update(AuctionSaleDeclarationRequest $request, AuctionSaleDeclaration $auctionSaleDeclaration)
    {
        return response($this->auctionSaleDeclarationRepository->update($auctionSaleDeclaration, $request->validated()));
    }

    public function generateCertificate(AuctionSaleDeclaration $auctionSaleDeclaration)
    {
        return response($auctionSaleDeclaration->generateCertificate(stream: true, view: $auctionSaleDeclaration->certificateView()));
    }

    public function destroy(AuctionSaleDeclaration $auctionSaleDeclaration)
    {
        [$success, $result] = $this->auctionSaleDeclarationRepository->destroy($auctionSaleDeclaration);

        return response($result, $success ? 200 : 422);
    }

    public function addVehicle(AddVehicleInAuctionSaleDeclarationRequest $request, AuctionSaleDeclaration $auctionSaleDeclaration)
    {
        return response($this->auctionSaleDeclarationRepository->addVehicle($auctionSaleDeclaration, $request->validated()));
    }

    public function removeVehicle(RemoveVehicleFromAuctionSaleDeclarationRequest $request, AuctionSaleDeclaration $auctionSaleDeclaration)
    {
        return response($this->auctionSaleDeclarationRepository->removeVehicle($auctionSaleDeclaration, $request->validated()));
    }

    public function addOfficial(AddOfficialInAuctionSaleDeclarationRequest $request, AuctionSaleDeclaration $auctionSaleDeclaration)
    {
        return response($this->auctionSaleDeclarationRepository->addOfficial($auctionSaleDeclaration, $request->validated()));
    }

    public function removeOfficial(RemoveOfficialFromAuctionSaleDeclarationRequest $request, AuctionSaleDeclaration $auctionSaleDeclaration)
    {
        return response($this->auctionSaleDeclarationRepository->removeOfficial($auctionSaleDeclaration, $request->validated()));
    }

    public function stats()
    {
        return response($this->auctionSaleDeclarationRepository->stats());
    }
}
