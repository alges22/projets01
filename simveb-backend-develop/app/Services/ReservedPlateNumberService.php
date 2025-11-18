<?php

namespace App\Services;

use App\Consts\NotificationNames;
use App\Enums\Status;
use App\Models\Config\ReservedPlateNumber;
use App\Models\DemandOtp;
use App\Models\Plate;
use App\Models\SimvebFile;
use App\Models\Treatment\PrintOrder;
use App\Notifications\NotificationSender;
use App\Repositories\Demand\DemandOtpRepository;
use App\Traits\Demands\PrintOrderTrait;
use App\Traits\UploadFile;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class ReservedPlateNumberService
{

    public function __construct()
    {}

    public function validateOrReject(array $data)
    {
        DB::beginTransaction();
        try {
            $printOrder = ReservedPlateNumber::find($data['reserved_plate_number_id']);

            $printOrder->update([
                'status' => $data['action'] == 'validate' ? Status::validated->name : Status::rejected->name,
                'validated_at' => $data['action'] == 'validate' ? now() : null,
                'rejected_at' => $data['action'] == 'reject' ? now() : null,
            ]);

            DB::commit();

            return ['message' => $data['action'] == 'validate' ? "Réservation validée." : "Réservation rejetée."];
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }
}
