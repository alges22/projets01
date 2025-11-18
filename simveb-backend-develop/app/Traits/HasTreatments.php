<?php
namespace App\Traits;



use App\Models\Treatment\Treatment;

trait HasTreatments
{

    public function treatments()
    {
        return $this->hasMany(Treatment::class);
    }

}
