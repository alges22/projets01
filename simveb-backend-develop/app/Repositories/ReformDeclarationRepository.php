<?php

namespace App\Repositories;

use App\Models\Auction\AuctionSaleVehicle;
use App\Models\Reform\ReformDeclaration;
use App\Traits\UploadFile;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class ReformDeclarationRepository
{
    use UploadFile;
    private AuctionSaleVehicleRepository $auctionSaleVehicleRepository;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->auctionSaleVehicleRepository = new AuctionSaleVehicleRepository;
    }

    public function store(array $data)
    {
        DB::beginTransaction();
        try {
            $data['auctioneer_id'] = getOnlineProfile()->id;

            $reportFile = Arr::pull($data, 'report');

            if ($reportPath = $this->saveFile($reportFile, 'reform_declaration_report')) {
                $data['report_path'] = $reportPath;
            }

            $vehiclesData = Arr::pull($data, 'auction_vehicles');

            $reformDeclaration = ReformDeclaration::create($data);

            foreach ($vehiclesData as $vehicleData) {
                $auctionSaleVehicle = AuctionSaleVehicle::find($vehicleData['id']);

                $divestingFile = Arr::pull($vehicleData, 'divesting_file');
                $pickupOrderFile = Arr::pull($vehicleData, 'pickup_order');

                if ($divestingFilePath = $this->auctionSaleVehicleRepository->saveVehicleFile($divestingFile, 'auction_sale_vehicle_divesting_file')) {
                    $vehicleData['divesting_file_path'] = $divestingFilePath;
                }

                if ($pickupOrderPath = $this->auctionSaleVehicleRepository->saveVehicleFile($pickupOrderFile, 'auction_sale_vehicle_pickup_order')) {
                    $vehicleData['pickup_order_path'] = $pickupOrderPath;
                }

                $vehicleData['reform_declaration_id'] = $reformDeclaration->id;

                $auctionSaleVehicle->update($vehicleData);
            }
            // TODO: put auction vehicle into another process to find profile/space of the buyer or send invitation

            DB::commit();

            return $reformDeclaration->load($reformDeclaration::relations());
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, __('errors.server_error'));
        }
    }

    public function update(ReformDeclaration $reformDeclaration, array $data)
    {
        DB::beginTransaction();
        try {
            $reportFile = Arr::pull($data, 'report');

            if ($reportFile) {
                if ($reportPath = $this->saveFile($reportFile, 'auction_sale_declaration_report')) {
                    $data['report_path'] = $reportPath;
                }
            }

            $reformDeclaration->update($data);

            $vehiclesData = Arr::pull($data, 'vehicles');

            $vehiclesData = Arr::pull($data, 'auction_vehicles');

            foreach ($vehiclesData as $vehicleData) {
                $auctionSaleVehicle = AuctionSaleVehicle::find($vehicleData['id']);

                $divestingFile = Arr::pull($vehicleData, 'divesting_file');
                $pickupOrderFile = Arr::pull($vehicleData, 'pickup_order');

                if ($divestingFile && $divestingFilePath = $this->auctionSaleVehicleRepository->saveVehicleFile($divestingFile, 'auction_sale_vehicle_divesting_file')) {
                    $vehicleData['divesting_file_path'] = $divestingFilePath;
                }

                if ($pickupOrderFile && $pickupOrderPath = $this->auctionSaleVehicleRepository->saveVehicleFile($pickupOrderFile, 'auction_sale_vehicle_pickup_order')) {
                    $vehicleData['pickup_order_path'] = $pickupOrderPath;
                }

                $auctionSaleVehicle->update($vehicleData);
            }
            // TODO: put auction vehicle into another process to find profile/space of the buyer or send invitation

            DB::commit();

            return $reformDeclaration->load($reformDeclaration::relations());
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, __('errors.server_error'));
        }
    }
}
