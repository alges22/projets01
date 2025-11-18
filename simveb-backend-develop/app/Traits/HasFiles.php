<?php
namespace App\Traits;



use App\Models\SimvebFile;

trait HasFiles
{
    public function file()
    {
        return $this->morphOne(SimvebFile::class,"model","model_type")
            ->where('type',SimvebFile::FILE);
    }

    public function files()
    {
        return $this->morphMany(SimvebFile::class,"model","model_type")
            ->where('type',SimvebFile::FILE);
    }

}
