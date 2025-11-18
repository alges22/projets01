<?php

namespace App\Services;

class TownDistrictVillageService
{
    public function __construct()
    {
    }

    public function getDataForLocation($modelGetId, $modelGetData, $foreignkey)
    {
        try {
            $name = request()->input('name');
            $dataId = $modelGetId::whereRaw('LOWER(name) = LOWER(?)', $name)->value('id');
            if ($dataId !== null)
            {
                $data = $modelGetData::select(['id', 'code', 'name'])->where($foreignkey, $dataId)->get();
                if (sizeof($data) > 0)
                {
                    return ['data' => $data];
                }else{
                    return ['error' => 'Aucune correspondance n\'a été trouvée'];
                }
            } else {
                return ['error' => 'La donnée renseignée n\'existe pas'];
            }
        }catch (\Exception $exception) {
            return ['error' => 'Une erreur est survenue'];
        }
    }
}
