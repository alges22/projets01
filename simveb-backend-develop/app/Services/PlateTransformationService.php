<?php

namespace App\Services;

use App\Enums\Status;
use App\Http\Requests\PlateTransformationRequest;
use App\Models\GrayCard;
use App\Models\Immatriculation\Immatriculation;
use App\Models\PlateTransformation;
use App\Repositories\PlateTransformationRepository;
use App\Repositories\Vehicle\VehicleOwnerRepository;
use App\Repositories\Vehicle\VehicleRepository;
use App\Services\Treatment\TreatmentService;
use App\Traits\UploadFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

/**
 * @property TreatmentService $treatmentService
 */
class PlateTransformationService
{
    use UploadFile;

    private VehicleRepository $vehicleRepository;
    private VehicleOwnerRepository $vehicleOwnerRepository;
    private PlateTransformationRepository $repository;

    public function __construct()
    {
        $this->repository = new PlateTransformationRepository;
        $this->vehicleRepository = new VehicleRepository;
        $this->vehicleOwnerRepository = new VehicleOwnerRepository;
        $this->treatmentService = new TreatmentService;
    }

    public function create()
    {
        return [];
    }

    /**
     * @param PlateTransformationRequest $request
     * @return Model|void|null
     */
    public function store(PlateTransformationRequest $request)
    {
        DB::beginTransaction();
        try
        {
            $vehicleOwner = $request->immatriculation_id
                ? Immatriculation::find($request->immatriculation_id)->load(Immatriculation::relations())->refresh()->vehicle->owner
                : GrayCard::find($request->gray_card_id)->load(GrayCard::relations())->refresh()->vehicleOwner;
            /* if (auth()->user()->identity_id != $vehicleOwner->identity_id) {
                return response()->json(['message' => 'Vous n\'êtes pas le propriétaire de ce véhicule']);
            } */

            if (is_null($request->immatriculation_id) || $request->immatriculation_id === '') {
                $request->merge([
                    'immatriculation_id' => GrayCard::find($request->gray_card_id)->immatriculation_id,
                ]);
            } elseif (is_null($request->gray_card_id) ||  $request->gray_card_id === '') {
                $request->merge([
                    'gray_card_id' => GrayCard::where('immatriculation_id',$request->immatriculation_id)->get()->last()->id,
                ]);
            }
            $request->merge([
                'reference' => $this->generateUniqueReference(),
                'vehicle_owner_id' => $vehicleOwner->id,
            ]);

            $plateTransformation = $this->repository->store($request->only([
                'gray_card_id',
                'immatriculation_id',
                'comment',
                'service_id',
                'payment_status',
                'vehicle_owner_id',
                'reference',
                'vin',
                'number_of_seats',
                'vehicle_category_id',
                'engin_number',
                'energy_type_id'
            ]));

            if ($request->has('documents')){
                foreach ($request->documents as $document){
                    $documentId = $document['document_type_id'];
                    $file = $document['file'];
                    $filePath = $this->saveFile($file, 'plate_transformations');
                    if ($filePath){
                        $this->createFile([
                            "path" => $filePath,
                            "model_type" => $plateTransformation::class,
                            "model_id" => $plateTransformation->id,
                            "file_type_id" => $documentId
                        ]);
                    }
                }
            }

            DB::commit();
            return $plateTransformation->refresh();
        }catch (\Exception $exception)
        {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,__('errors.server_error'));
        }
    }

    /**
     * @param PlateTransformation $plateTransformation
     * @param $data
     * @return Model|void|null
     */
    public function update(PlateTransformation $plateTransformation, $data,$request = null)
    {
        DB::beginTransaction();
        try
        {
            $plateTransformation = $this->repository->update($plateTransformation,$data);

            if ($request?->has('documents') && $imagePaths = $this->saveMultipleFiles($request, 'plate_transformations', "documents")){
                foreach ($imagePaths as $imagePath)
                {
                    $this->createFile([
                        "path" => $imagePath,
                        "model_type" => $plateTransformation::class,
                        "model_id" => $plateTransformation->id
                    ]);
                }
            }
            DB::commit();
            return $plateTransformation->refresh();
        }catch (\Exception $exception)
        {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,__('errors.server_error'));
        }
    }

    public function getOwnerDemands($vehicleOwnerId)
    {
        return $this->repository
            ->model
            ->newQuery()
            ->with(['vehicleOwner', 'extraServices:id,name,description,cost'])
            ->where('vehicle_owner_id',$vehicleOwnerId)
            ->get();
    }

    public function calculateDemandCost($demandId)
    {
        $demand = $this->repository->model
            ->newQuery()
            ->with('service')
            // ->with('service','extraServices')
            ->find($demandId);
        $totalCost = $demand->service->cost ? $demand->service->cost : $demand->service->type->cost;

        // foreach ($demand->extraServices as $service)
        // {
        //     $totalCost += $service->cost;
        // }

        return $totalCost;
    }

    public function generateUniqueReference()
    {
        do{
            $reference = generateReference('PT-');
        }while(PlateTransformation::where('reference', $reference)->exists());

        return $reference;
    }

    public function submitDemand($demandId)
    {
        $demand = $this->repository->find($demandId);
        //init treatment
        $treatment  = $demand->treatments()->create();
        $demand->update([
            'status' => Status::submitted->name,
            'submitted_at' => now(),
            'active_treatment_id' => $treatment->id
        ]);

        $treatment
            ->update([
            'status' => Status::submitted->name,
            'submitted_at' => now()
        ]);

        //assign automatically to service
        if (serviceExist( $treatment->model->service->target_organization_id)){
            $this->treatmentService->assignDemandToService([
                'treatment_id' => $treatment->id,
                'service_id' =>  $treatment->model->service->target_organization_id
            ]);
        }

        return $demand->refresh();
    }

}
