<?php

namespace App\Repositories;

use App\Models\Auction\AuctionSaleVehicle;
use App\Traits\UploadFile;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuctionSaleVehicleRepository
{
    use UploadFile;
    public function saveVehicleFile($file, $name): array|bool
    {
        return $this->saveFile($file, "$name");
    }

    public function update(AuctionSaleVehicle $auctionSaleVehicle, array $data)
    {
        DB::beginTransaction();
        try {
            $divestingFile = Arr::pull($data, 'divesting_file');

            if ($divestingFile) {
                if ($divestingPath = $this->saveFile($divestingFile, 'auction_sale_vehicle_divesting_file')) {
                    $data['divesting_file_path'] = $divestingPath;
                }
            }
            $pickupOrderFile = Arr::pull($data, 'pickup_order');

            if ($pickupOrderFile) {
                if ($pickupOrderPath = $this->saveFile($pickupOrderFile, 'auction_sale_vehicle_pickup_order')) {
                    $data['pickup_order_path'] = $pickupOrderPath;
                }
            }

            if (empty($data['custom_receipt_reference'])) {
                unset($data['custom_receipt_reference']);
            }

            $auctionSaleVehicle->update($data);

            DB::commit();

            return $auctionSaleVehicle->load($auctionSaleVehicle::relations());
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, __('errors.server_error'));
        }
    }
}
