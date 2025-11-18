<?php

namespace App\Traits;

use App\Models\PopoteImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

trait ProcessImages
{

    protected function saveFile(Request $request,$folder,$field="file")
    {
        $file =  $request->file($field);
        $extension = $file->getClientOriginalExtension();
        $fileName = Str::random(32);
        $filePath = $folder.'/'.$fileName.'.'.$extension;
        $fileCroppedPath = $folder.'/cropped/'.$fileName.'.jpg';

        Storage::disk('s3')->put($filePath,file_get_contents($file), $file);

        if (Str::contains($file->getMimeType(),'image')){
            // save file as jpg with medium quality
            $img = Image::make( Storage::disk('s3')->get($filePath));
            // crop the best fitting 1:1 ratio (200x200) and resize to 200x200 pixel
            $croppedImage = $img->fit(600,600,function($constraint){
                $constraint->upsize();
            })->encode('jpg');

            Storage::disk('s3')->put($fileCroppedPath,$croppedImage->getEncoded());

            $fileUrl = [
                "original"=>config('consts.aws_bucket_url').$filePath,
                "cropped"=>config('consts.aws_bucket_url').$fileCroppedPath,
                "type"=>$file->getMimeType()
            ];

        }else{
            $fileUrl = ["url"=>config('consts.aws_bucket_url') . $folder . "/" . $fileName.'.'.$extension,"type"=>$file->getMimeType()];
        }

        return $fileUrl;

    }

    protected function saveMultipleFiles(Request $request,$folder)
    {
        $urls = [];

        foreach ($request->file('files') as $file){
            $extension = $file->getClientOriginalExtension();
            $fileName = Str::random(32);
            $filePath = $folder.'/'.$fileName.'.'.$extension;
            $fileCroppedPath = $folder.'/cropped/'.$fileName.'.jpg';
            Storage::disk('s3')->put($filePath,file_get_contents($file), $file);

            if (Str::contains($file->getMimeType(),'image')){
                // save file as jpg with medium quality
                $img = Image::make( Storage::disk('s3')->get($filePath));
                // crop the best fitting 1:1 ratio (200x200) and resize to 200x200 pixel
                $croppedImage = $img->fit(600,600,function($constraint){
                    $constraint->upsize();
                })->encode('jpg');
                Storage::disk('s3')->put($fileCroppedPath,$croppedImage->getEncoded());

                $fileUrl = [
                    "original"=>config('consts.aws_bucket_url').$filePath,
                    "cropped"=>config('consts.aws_bucket_url').$fileCroppedPath,
                    "type"=>$file->getMimeType()
                ];

            }else{
                $fileUrl = ["url"=>config('consts.aws_bucket_url') . $folder . "/" . $fileName.'.'.$extension,"type"=>$file->getMimeType()];
            }
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
            ->updateOrCreate(["imageable_id"=>$data['imageable_id']],
            $data);
    }
}
