<?php
namespace App\Traits;



use App\Models\SimvebFile;

trait HasImages
{
    public function image()
    {
        return $this->morphOne(SimvebFile::class,"model","model_type")
            ->where('type',SimvebFile::IMAGE);
    }

    public function images()
    {
        return $this->morphMany(SimvebFile::class,"model","model_type")
            ->where('type',SimvebFile::IMAGE);
    }

}
