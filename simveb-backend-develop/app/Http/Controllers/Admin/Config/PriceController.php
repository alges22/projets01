<?php

namespace App\Http\Controllers\Admin\Config;

use App\Http\Controllers\Controller;
use App\Http\Requests\PriceRequest;
use App\Models\Config\OwnerType;
use App\Models\Config\Price;
use App\Models\Config\Service;
use App\Models\Vehicle\VehicleCategory;
use App\Models\Vehicle\VehicleCharacteristic;
use App\Models\Vehicle\VehicleCharacteristicCategory;
use App\Models\Vehicle\VehicleType;
use App\Traits\CrudRepositoryTrait;

class PriceController extends Controller
{
    use CrudRepositoryTrait;

    public function __construct()
    {
        $this->initRepository(Price::class);
        $this->authorizeResource(Price::class,'price');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->successResponse($this->repository->getAll(true,[
            'service:id,name',
            'ownerType:id,label',
            'vehicleType:id,label',
            'characteristic:id,category_id' => ['category:id,name'],
        ]));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return $this->successResponse($this->createData());
    }

    private function createData()
    {
        return [
            'characteristics' => VehicleCharacteristic::query()
                ->with(['category:id,label'])
                ->select(['id','category_id','value'])
                ->get(),
            'vehicle_types' => VehicleType::query()->select(['id','label'])->get(),
            'owner_types' => OwnerType::query()->select(['label','id'])->get(),
            'vehicle_categories' => VehicleCategory::query()->select(['id','label'])->get(),
            'services' => Service::query()->select(['id','name'])->get(),
            'categories' => VehicleCharacteristicCategory::query()->select(['id','label'])->get(),
        ];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PriceRequest $request)
    {
        return $this->createdResponse($this->repository->store($request->validated()));
    }

    /**
     * Display the specified resource.
     */
    public function show(Price $price)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Price $price)
    {
        return $this->successResponse($this->createData() + ['price' => $price]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PriceRequest $request, Price $price)
    {
        return $this->successResponse($request->validated(),$price);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Price $price)
    {
        return $this->successResponse($this->repository->destroy($price));
    }
}
