<?php

namespace App\Traits;

use App\Models\PopoteImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

trait ProcessFiles
{

    protected function saveFile(Request $request,$folder,$field="file")
    {
        $this->createFolder(public_path("storage/".$folder));
        $file =  $request->file($field);
        $fileName = Str::random(16).'.'.Str::slug($file->getClientOriginalExtension());
        $file->move(public_path('storage/'.$folder), $fileName);

        return asset('storage/'.$folder."/".$fileName);
    }

    protected function saveMultipleFiles(Request $request,$folder)
    {
        $this->createFolder(public_path("storage/".$folder));
        $urls = [];

        foreach ($request->file('files') as $file){
            $fileName = Str::random(16).'.'.Str::slug($file->getClientOriginalExtension());
            $file->move(public_path('storage/'.$folder),$fileName);

            $fileUrl = asset('storage/'.$folder."/".$fileName);
            array_push($urls,$fileUrl);
        }
        return $urls;
    }

    protected function createFolder($path)
    {
        if(!file_exists($path)){
            File::makeDirectory($path);
            File::makeDirectory($path.'/cropped');
        }
    }

    protected function createImage($data)
    {
        return PopoteImage::query()->create($data);
    }
    protected function updateImage($data)
    {
        return PopoteImage::query()
            ->where("imageable_id",$data['imageable_id'])
            ->update($data);
    }


}
