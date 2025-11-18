<?php

namespace App\Services\Immatriculation;

use App\Enums\Status;
use App\Models\Config\ReservedPlateNumber;
use App\Models\Immatriculation\ImmatriculationLabel;

class SuggestionService
{

    public function checkLabelIsAvailable(string $label ): array
    {
        $labelIsAvailable = ImmatriculationLabel::query()
            ->where('label', $label)
            ->whereIn('status', [Status::validated->name, Status::active->name])
            ->doesntExist();

        return [
          'available' => $labelIsAvailable,
          'message' => !$labelIsAvailable ? 'Oups! Ce label est déjà pris' : 'Youpi! Label disponible'
        ];
    }

    public function checkNumberIsAvailable(int $number): array
    {
        $lastImmNumber = getLastImmNumber(null);
        $numberIsNotReserved = true;
        $numberNotExist = true;

        if ($lastImmNumber){
            $numberIsNotReserved = ReservedPlateNumber::query()
                ->where([
                    'numeric_label' => $number,
                    'alphabetic_label' => $lastImmNumber->alphabetic_label
                ])->doesntExist();
            $numberNotExist = (int)$lastImmNumber->numeric_label < $number;
        }

        return $numberNotExist && $numberIsNotReserved ?
            [
                'available' => true,
                'message' =>  'Youpi! Le numéro est disponible'
            ] :
            [
                'available' => false,
                'message' => 'Oups! Ce numéro est déjà pris'
            ];
    }

    public function suggestNumber(string $template): mixed
    {
        $numberService =  new ImmatriculationPrestigeNumberService;

        return $numberService->getSuggestions($template);
    }

}
