<?php

namespace App\Http\Controllers\Client\Demand;

use App\Http\Controllers\Controller;
use App\Models\Order\Demand;
use App\Repositories\Demand\DemandRepository;
use App\Services\Demand\DemandService;
use Illuminate\Http\Request;

class DemandAttachmentController extends Controller
{
    public function __construct(
        private readonly DemandRepository $repository,
        private readonly DemandService $service
    )
    {}

    public function update(Request $request, Demand $demand)
    {
        return response($this->service->update($demand, $request->validate([
            'documents' => ['required', 'array',],
            'documents.*.type_id' => ['required'],
            'documents.*.file' => ['required','file'],
        ]), $request));
    }
}
