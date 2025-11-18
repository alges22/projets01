<?php

namespace App\Repositories;

use App\Models\Auction\AuctionSaleDeclaration;
use App\Models\Vehicle\Vehicle;
use App\Traits\UploadFile;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuctionSaleDeclarationRepository
{
    use UploadFile;

    public function showByReference(string $reference)
    {
        $auctionSaleDeclaration = AuctionSaleDeclaration::where(['reference' => $reference])->first();

        return !$auctionSaleDeclaration ? [false, ['message' => 'Déclaration de vente aux enchères introuvable']] : [true, $auctionSaleDeclaration->load($auctionSaleDeclaration::relations())->append('official_identities')];
    }

    public function store(array $data)
    {
        DB::beginTransaction();
        try {
            $data['auctioneer_id'] = getOnlineProfile()->id;

            $reportFile = Arr::pull($data, 'report');

            if ($reportPath = $this->saveFile($reportFile, 'auction_sale_declaration_report')) {
                $data['report_path'] = $reportPath;
            }

            $vehiclesData = Arr::pull($data, 'vehicles');

            $auctionSaleDeclaration = AuctionSaleDeclaration::create($data);

            foreach ($vehiclesData as $vehicleData) {
                $vehicleData['vehicle_id'] = Vehicle::where('vin', Arr::pull($vehicleData, 'vehicle_vin'))->first()->id;
                $auctionSaleDeclaration->saledVehicles()->create($vehicleData);
            }
            // TODO: put auction vehicle into another process to find profile/space of the buyer or send invitation

            DB::commit();

            return $auctionSaleDeclaration->load(AuctionSaleDeclaration::relations());
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, __('errors.server_error'));
        }
    }

    public function update(AuctionSaleDeclaration $auctionSaleDeclaration, array $data)
    {
        DB::beginTransaction();
        try {
            unset($data['auction_sale_declaration_id']);

            $reportFile = Arr::pull($data, 'report');
            $vehiclesData = Arr::pull($data, 'vehicles');

            if ($reportFile) {
                if ($reportPath = $this->saveFile($reportFile, 'auction_sale_declaration_report')) {
                    $data['report_path'] = $reportPath;
                }
            }

            if (empty($data['instituton_id'])) {//TODO: allow null instituton_id when it is not for government
                unset($data['instituton_id']);
            }

            if (empty($data['officials'])) {
                unset($data['officials']);
            }

            $auctionSaleDeclaration->update($data);

            if ($vehiclesData) {
                $auctionSaleDeclaration->saledVehicles()->delete();
                foreach ($vehiclesData as $vehicleData) {
                    $vehicleData['vehicle_id'] = Vehicle::where('vin', Arr::pull($vehicleData, 'vehicle_vin'))->first()->id;
                    $auctionSaleDeclaration->saledVehicles()->create($vehicleData);
                }
            }
            // TODO: put auction vehicle into another process to find profile/space of the buyer or send invitation

            DB::commit();

            return $auctionSaleDeclaration->load(AuctionSaleDeclaration::relations());
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, __('errors.server_error'));
        }
    }

    public function destroy(AuctionSaleDeclaration $auctionSaleDeclaration)
    {
        if ($auctionSaleDeclaration->reformDeclarations()->count() > 0) {
            return [false, ['message' => 'Impossible de supprimer une déclaration de vente aux enchères ayant une/des réforme(s).']];
        }

        DB::beginTransaction();
        try {
            $auctionSaleDeclaration->saledVehicles()->delete();
            $auctionSaleDeclaration->delete();

            DB::commit();

            return [true, ['message' => 'Déclaration de vente aux enchères supprimée avec succès.']];
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, __('errors.server_error'));
        }
    }

    public function addVehicle(AuctionSaleDeclaration $auctionSaleDeclaration, array $data)
    {
        $vehicle = Vehicle::where('vin', Arr::pull($data, 'vehicle_vin'))->first();
        $data['vehicle_id'] = $vehicle->id;

        $auctionSaleDeclaration->saledVehicles()->create($data);

        return $auctionSaleDeclaration->load(AuctionSaleDeclaration::relations());
    }

    public function removeVehicle(AuctionSaleDeclaration $auctionSaleDeclaration, array $data)
    {
        $auctionSaleDeclaration->saledVehicles()->where('vehicle_id', $data['vehicle_id'])->delete();

        return $auctionSaleDeclaration->load(AuctionSaleDeclaration::relations());
    }

    public function addOfficial(AuctionSaleDeclaration $auctionSaleDeclaration, array $data)
    {
        $officials = $auctionSaleDeclaration->officials;
        $officials[] = $data;

        $auctionSaleDeclaration->update(['officials' => $officials]);

        return $auctionSaleDeclaration->load(AuctionSaleDeclaration::relations());
    }

    public function removeOfficial(AuctionSaleDeclaration $auctionSaleDeclaration, array $data)
    {
        $officials = collect($auctionSaleDeclaration->officials);

        $officials = $officials->where('npi', '!=', $data['npi'])->toArray();

        $auctionSaleDeclaration->update(['officials' => $officials]);

        return $auctionSaleDeclaration->load(AuctionSaleDeclaration::relations());
    }

    public function stats()
    {
        return [
            'total' => AuctionSaleDeclaration::count(),
        ];
    }
}
