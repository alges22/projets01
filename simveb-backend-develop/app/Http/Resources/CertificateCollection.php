<?php

namespace App\Http\Resources;

use App\Services\IdentityService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CertificateCollection extends ResourceCollection
{
    private IdentityService $identityService;
    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->identityService = new IdentityService;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->collection->transform(function ($item) {
            $data = [
                'id' => $item->id,
                'reference' => $item->model->reference,
                'title' => $item->title,
                'model_type' => $item->model_type,
                'model_id' => $item->model_id,
                'profile_id' => $item->profile_id,
                'institution_id' => $item->institution_id,
                'created_at' => $item->created_at,
            ];
            $data['old_owner'] = new IdentityResource($item->model->vehicleOwner->identity);
            $data['new_owner'] = $item->model->new_owner_npi ? $this->identityService->showByNpi($item->model->new_owner_npi)->response()->getData(true) : null;
            $data['vehicle'] = new ClientVehicleCollection([$item->model->vehicle]);
            return $data;
        })->toArray();
    }
}
