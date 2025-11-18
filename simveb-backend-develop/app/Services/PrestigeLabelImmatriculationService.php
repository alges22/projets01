<?php

namespace App\Services;

use App\Enums\Status;
use App\Http\Requests\PrestigeLabelImmatriculationRequest;
use App\Models\Immatriculation\Immatriculation;
use App\Models\PrestigeLabelImmatriculation;
use App\Repositories\PrestigeLabelImmatriculationRepository;
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
class PrestigeLabelImmatriculationService
{
    use UploadFile;

    private VehicleRepository $vehicleRepository;
    private VehicleOwnerRepository $vehicleOwnerRepository;
    private PrestigeLabelImmatriculationRepository $repository;

    public function __construct()
    {
        $this->repository = new PrestigeLabelImmatriculationRepository;
        $this->vehicleRepository = new VehicleRepository;
        $this->vehicleOwnerRepository = new VehicleOwnerRepository;
        $this->treatmentService = new TreatmentService;
    }

    public function create()
    {
        return [];
    }

    /**
     * @param PrestigeLabelImmatriculationRequest $request
     * @return Model|void|null
     */
    public function store(PrestigeLabelImmatriculationRequest $request)
    {
        DB::beginTransaction();
        try
        {
            $vehicleOwner = Immatriculation::find($request->immatriculation_id)->load(Immatriculation::relations())->refresh()->vehicle->owner;
            if (auth()->user()->identity->id != $vehicleOwner->identity_id) {
                return response()->json(['message' => 'Vous n\'êtes pas le propriétaire de ce véhicule']);
            }

            $request->merge([
                'reference' => $this->generateUniqueReference(),
                'vehicle_owner_id' => $vehicleOwner->id,
            ]);

            $prestigeLabelImmatriculation = $this->repository->store($request->only([
                'desired_label',
                'comment',
                'service_id',
                'payment_status',
                'immatriculation_id',
                'reference',
                'vehicle_owner_id'
            ]));

            if ($request->has('documents')){
                foreach ($request->documents as $document){
                    $documentId = $document['document_type_id'];
                    $file = $document['file'];
                    $filePath = $this->saveFile($file, 'prestige_label_immatriculations');
                    if ($filePath){
                        $this->createFile([
                            "path" => $filePath,
                            "model_type" => $prestigeLabelImmatriculation::class,
                            "model_id" => $prestigeLabelImmatriculation->id,
                            "file_type_id" => $documentId
                        ]);
                    }
                }
            }

            DB::commit();
            return $prestigeLabelImmatriculation->refresh();
        }catch (\Exception $exception)
        {
            DB::rollBack();
            Log::debug($exception);
            abort(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,__('errors.server_error'));
        }
    }

    /**
     * @param PrestigeLabelImmatriculation $prestigeLabelImmatriculation
     * @param $data
     * @return Model|void|null
     */
    public function update(PrestigeLabelImmatriculation $prestigeLabelImmatriculation, $data,$request = null)
    {
        DB::beginTransaction();
        try
        {
            $prestigeLabelImmatriculation = $this->repository->update($prestigeLabelImmatriculation,$data);

            if ($request?->has('documents') && $imagePaths = $this->saveMultipleFiles($request, 'prestige_label_immatriculations', "documents")){
                foreach ($imagePaths as $imagePath)
                {
                    $this->createFile([
                        "path" => $imagePath,
                        "model_type" => $prestigeLabelImmatriculation::class,
                        "model_id" => $prestigeLabelImmatriculation->id
                    ]);
                }
            }
            DB::commit();
            return $prestigeLabelImmatriculation->refresh();
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
            $reference = generateReference('PLI-');
        }while(PrestigeLabelImmatriculation::where('reference', $reference)->exists());

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

        $treatment->update([
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
        /*
            sendMail(
                null,
                $demand->vehicleOwner->identity,
                NotificationNames::PLATE_DUPLICATE_SUBMITTED,
                [
                    'reference' => $demand->reference
                ]
            );*/

        return $demand->refresh();
    }
    /* use Spatie\LaravelPdf\Facades\Pdf;
    Pdf::view('welcome')->save(public_path('storage/'.'essai.pdf'));


Pdf::view('pdf.invoice', ['invoice' => $invoice])
    ->save('/some/directory/invoice.pdf'); */

}
