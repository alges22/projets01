<?php

namespace App\Http\Controllers;

use App\Enums\InstitutionTypesEnum;
use App\Http\Requests\GovVehicleRequest;
use App\Models\Institution\Institution;
use App\Models\Institution\InstitutionType;
use App\Models\Vehicle\GovVehicle;
use App\Repositories\Vehicle\GoVehicleRepository;
use Illuminate\Http\Request;

class GovVehicleController extends Controller
{
    public function __construct(private readonly GoVehicleRepository $goVehicleRepository)
    {
        $this->authorizeResource(GovVehicle::class,'gov_vehicle');
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->successResponse($this->goVehicleRepository->getAll());
    }

    public function create()
    {
        return [
            'import_model' => file_exists(public_path('format-import/gov_vehicle_template.xlsx')) ? asset('format-import/gov_vehicle_template.xlsx') : "",
            'institutions' => Institution::query()
              ->where('type_id', InstitutionType::query()->where('name', InstitutionTypesEnum::gov_institution->name)->first()?->id)
              ->select(['id','name'])
              ->get()
        ];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(GovVehicleRequest $request)
    {
        return $this->createdResponse($this->goVehicleRepository->store($request->validated()));
    }

    /**
     * Display the specified resource.
     */
    public function show(GovVehicle $govVehicle)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(GovVehicleRequest $request, GovVehicle $govVehicle)
    {
        return $this->createdResponse($this->goVehicleRepository->update($govVehicle, $request->validated()));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GovVehicle $govVehicle)
    {
        return response($this->goVehicleRepository->destroy($govVehicle));
    }
}
