<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait DeleteImageTrait
{

    public function deleteImage($id)
    {
        return DB::table("fct_images")->where('id',$id)->delete();
    }
}
