<?php

namespace App\Repositories;

use App\Models\Plate\PlateColor;
use Illuminate\Support\Str;

class PlateColorRepository
{

    /**
     * @param array $data
     * @return PlateColor
     */
    public function store(array $data)
    {
        $data['name'] = Str::slug($data['label'], '_');

        return PlateColor::create($data);
    }

    /**
     * @param PlateColor $plateColor
     * @param array $data
     * @return PlateColor
     */
    public function update(PlateColor $plateColor, array $data)
    {
        $data['name'] = Str::slug($data['label'], '_');
        $plateColor->update($data);

        return $plateColor;
    }
}
