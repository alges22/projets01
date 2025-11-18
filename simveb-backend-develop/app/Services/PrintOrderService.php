<?php

namespace App\Services;

use App\Consts\AvailableServiceType;
use App\Consts\NotificationNames;
use App\Enums\Status;
use App\Models\DemandOtp;
use App\Models\Order\Demand;
use App\Models\Plate;
use App\Models\SimvebFile;
use App\Models\Treatment\PrintOrder;
use App\Repositories\Demand\DemandOtpRepository;
use App\Traits\Demands\PrintOrderTrait;
use App\Traits\UploadFile;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PrintOrderService
{
    use PrintOrderTrait, UploadFile;

    public function __construct(private readonly DemandOtpRepository $demandOtpRepository) {}

    public function confirmAffectation(array $data)
    {
        DB::beginTransaction();

        try {
            $printOrder = PrintOrder::find($data['print_order_id']);

            $authorization = DemandOtp::find($data['authorization_id']);

            $authorization->update([
                'model_id' => $printOrder->id,
                'model_type' => PrintOrder::class
            ]);

            $printOrder->update([
                'status' => Status::active->name,
                'plate_affected_at' => now(),
                'institution_id' => getOnlineProfile()->institution->id,
            ]);

            DB::commit();

            return ['message' => 'Dossier affecté.'];
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function printGrayCard(array $data)
    {
        DB::beginTransaction();
        try {
            $demand = Demand::find($data['demand_id']);

            $printOrder = $demand->latestPrintOrder;

            $serviceCode = $demand->service->type->code;

            $grayCard = match ($serviceCode) {
                AvailableServiceType::GRAY_CARD_DUPLICATE => function () use ($printOrder) {
                    $newGrayCard = $printOrder->demand->model->newGrayCard;

                    $newGrayCard->update(['print_order_id' => $printOrder->id]);

                    return $newGrayCard;
                },
                AvailableServiceType::VEHICLE_TRANSFORMATION => function () use ($printOrder) {
                    $vehicleTransformation = $printOrder->demand->model;

                    $immatriculation = $vehicleTransformation->vehicle->frontPlate ? $vehicleTransformation->vehicle->frontPlate->immatriculation : $vehicleTransformation->vehicle->backPlate->immatriculation;

                    $grayCard = $immatriculation->activeGrayCard;

                    $grayCard->update(['print_order_id' => $printOrder->id]);

                    return $grayCard;
                },
                default => function () use ($printOrder, $serviceCode) {
                    $demandModel = $printOrder->demand->model;

                    $immatriculation = $serviceCode == AvailableServiceType::RE_IMMATRICULATION ? $demandModel->immatriculation : $demandModel;

                    $grayCard = $immatriculation->activeGrayCard;

                    $grayCard->update(['print_order_id' => $printOrder->id]);

                    return $grayCard;
                }
            };

            if ($grayCard) {
                //TODO: attach qr code to gray card
            }

            $onlineProfile = getOnlineProfile();

            $printOrder->update([
                'status' => Status::validated->name,
                'validated_at' => now(),
                'card_status' => Status::printed->name,
                'card_printer_id' => $onlineProfile->id,
                'card_printed_at' => now(),
                'card_validator_id' => $onlineProfile->id,
                'card_validated_at' => now(),
                'card_observations' => '-',
            ]);

            // $demand->update([
            //     'status' => Status::print_order_validated->name,
            // ]);

            sendMail(
                null,
                $printOrder->demand->vehicle->owner->identity,
                NotificationNames::GRAY_CARD_PRINTED,
                [
                    'reference' => $printOrder->reference,
                ]
            );

            DB::commit();

            // TODO: also return qr code
            return ['message' => 'La carte grise a été imprimée',];
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    public function printPlate(array $data)
    {
        DB::beginTransaction();
        try {
            $printOrder = PrintOrder::find($data['print_order_id']);

            $printOrder->images()->delete();
            foreach ($data['images'] as $image) {
                $imageInfo = $this->saveFile($image, 'printed_plate_images');
                $printOrder->images()->create([
                    'path' => $imageInfo,
                    'type' => SimvebFile::IMAGE,
                ]);
            }

            $serviceCode = $printOrder->demand->service->type->code;
            $demandModel = $printOrder->demand->model;

            switch ($serviceCode) {
                case AvailableServiceType::IMMATRICULATION_STANDARD:
                case AvailableServiceType::IMMATRICULATION_PRESTIGE_NUMBER:
                case AvailableServiceType::IMMATRICULATION_PRESTIGE_LABEL:
                case AvailableServiceType::IMMATRICULATION_PRESTIGE_NUMBER_LABEL:
                case AvailableServiceType::RE_IMMATRICULATION:
                case AvailableServiceType::PLATE_TRANSFORMATION: {
                        // two sides
                        if (isset($data['front_plate']) && $data['front_plate']) {
                            $this->attachPlate($printOrder, $data, 'front', $serviceCode, $demandModel);
                        }

                        if (isset($data['back_plate']) && $data['back_plate']) {
                            $this->attachPlate($printOrder, $data, 'back', $serviceCode, $demandModel);
                        }
                        break;
                    }
                case AvailableServiceType::PLATE_DUPLICATE: {
                        // one side || determine the side
                        $oldPlate = Plate::find($printOrder->demand->model->old_plate_id);

                        if ($oldPlate->id == $oldPlate->immatriculation->vehicle->front_plate_id) {
                            $this->attachPlate($printOrder, $data, 'front', $serviceCode, $demandModel);
                        } else {
                            $this->attachPlate($printOrder, $data, 'back', $serviceCode, $demandModel);
                        }
                        break;
                    }
                default:
                    break;
            }

            $onlineProfile = getOnlineProfile();

            $printOrder->update([
                // 'status' => Status::plate_printed->name,
                'plate_status' => Status::printed->name,
                'plate_printer_id' => $onlineProfile->id,
                'plate_printed_at' => now(),
            ]);

            $printOrder->demand->activeTreatment->update([
                'printer_id' => $onlineProfile->institution->id,
                'printed_at' => now(),
                'printed_by' => $onlineProfile->id,
            ]);

            sendMail(
                null,
                $printOrder->demand->vehicle->owner->identity,
                NotificationNames::PLATE_PRINTED,
                [
                    'reference' => $printOrder->reference,
                ]
            );

            DB::commit();

            return ['message' => 'La/les plaque·s ont été marqués comme imprimée·s.',];
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    private function attachPlate(PrintOrder $printOrder, array $data, string $side, string $serviceCode, $demandModel)
    {
        $plate = Plate::where('serial_number', $side == 'front' ? $data['front_plate'] : $data['back_plate'])->first();

        $plate->update([
            'immatriculation_id' => match ($serviceCode) {
                AvailableServiceType::IMMATRICULATION_STANDARD => $demandModel->id,
                AvailableServiceType::PLATE_TRANSFORMATION => $demandModel->vehicle->frontPlate ? $demandModel->vehicle->frontPlate->immatriculation_id : $demandModel->vehicle->backPlate->immatriculation_id,
                default => $demandModel->immatriculation_id,
            },
            'rfid' => match ($side) {
                'front' => $data['front_plate_rfid'] ?? null,
                'back' => $data['back_plate_rfid'] ?? null,
            },
            'is_duplicate' => $serviceCode == AvailableServiceType::PLATE_DUPLICATE,
        ]);
        $printOrder->plates()->attach([$plate->id => ['side' => $side]],);
    }

    public function validateOrRejectPrint(array $data)
    {
        DB::beginTransaction();
        try {
            $printOrder = PrintOrder::find($data['print_order_id']);

            $printOrder->update([
                'status' => $data['action'] == 'validate' ? Status::validated->name : Status::rejected->name,
                'plate_status' => $data['action'] == 'validate' ? Status::validated->name : Status::rejected->name,
                'validated_at' => $data['action'] == 'validate' ? now() : null,
                'rejected_at' => $data['action'] == 'reject' ? now() : null,
                'plate_validated_at' => $data['action'] == 'validate' ? now() : null,
                'plate_rejected_at' => $data['action'] == 'reject' ? now() : null,
                'plate_observations' => $data['observations'] ?? null,
                'plate_validator_id' => $data['action'] == 'validate' ? getOnlineProfile()->id : null,
                'plate_rejector_id' => $data['action'] == 'reject' ? getOnlineProfile()->id : null,
            ]);

            if ($data['action'] == 'validate') {
                $printOrder->demand->activeTreatment->update([
                    'print_observations' => $data['observations'] ?? null,
                ]);

                $vehicle = $printOrder->demand->vehicle;
                $vehicle->images()->delete();

                foreach ($data['images'] as $image) {
                    $imageInfo = $this->saveFile($image, 'vehicle_images');
                    $vehicle->images()->create([
                        'path' => $imageInfo,
                        'type' => SimvebFile::IMAGE,
                    ]);
                }

                if ($frontPlate = $printOrder->plates()->wherePivot('side', 'front')->first()) {
                    $vehicle->update(['front_plate_id' => $frontPlate->id]);
                }

                if ($backPlate = $printOrder->plates()->wherePivot('side', 'back')->first()) {
                    $vehicle->update(['back_plate_id' => $backPlate->id]);
                }

                if ($printOrder->demand->service->type->code == AvailableServiceType::PLATE_DUPLICATE) {
                    $printOrder->demand->model->update(['new_plate_id' => $printOrder->plates()->first()->id]);
                }

                // if ($printOrder->type == PrintOrderTypesEnum::plate->name) {
                $printOrder->demand->update([
                    'status' => Status::print_order_validated->name,
                ]);
                // }
            }

            $notifName = $data['action'] == 'validate' ? NotificationNames::PRINT_VALIDATED : NotificationNames::PRINT_REJECTED;

            $notifData = [
                'reference' => $data['action'] == 'validate' ? $printOrder->demand->reference : $printOrder->reference,
                'observation' => $data['observations'] ?? 'aucune',
            ];

            sendMail(
                null,
                $printOrder->demand->vehicle->owner->identity,
                $notifName,
                $notifData
            );

            if ($data['action'] == 'reject') {
                $this->emitPrintOrder($printOrder->demand);
            }

            DB::commit();

            return ['message' => $data['action'] == 'validate' ? 'Impression de plaque validée.' : 'Impression de plaque rejetée.'];
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }
}
