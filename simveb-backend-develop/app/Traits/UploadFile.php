<?php

namespace App\Traits;


use App\Models\SimvebFile;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

trait UploadFile
{

    protected function saveFile(Request|UploadedFile $request, $folder, $field="file"): bool|array
    {
        try {
            $this->createFolder(public_path("storage/".$folder));
            $file = $request instanceof UploadedFile ? $request : $request->file($field);
            $extension = $file->getClientOriginalExtension();
            $fileName = Str::random().".$extension";
            $fileOrgName = $file->getClientOriginalName();
            $file->move(public_path('storage/'.$folder), $fileName);

            return [
                'path' => "storage/$folder/$fileName",
                'name' => $fileOrgName,
                'type' => $this->getFileType($extension)
            ];
        }catch (Exception $exception)
        {
            Log::debug($exception);
            return false;
        }
    }

    protected function saveMultipleFiles($request,$folder,$field = "files"): mixed
    {
        try
        {
            $this->createFolder(public_path("storage/".$folder));
            $urls = [];

            foreach ($request->{$field} as $file)
            {
                $extension = $file->getClientOriginalExtension();
                $fileName = Str::random().".$extension";
                $file->move(public_path('storage/'.$folder),$fileName);
                $fileOrgName = $file->getClientOriginalName();

                $urls[] = [
                    'path' => "storage/$folder/$fileName",
                    'name' => $fileOrgName,
                    'type' => $this->getFileType($extension)
                ];
            }
            return $urls;

        }catch (Exception $exception)
        {
            Log::debug($exception);
            return false;
        }

    }

    /**
     * @return bool|array
     */
    protected function saveBase64File(string $base64String, $folder): bool|array
    {
        try {
            $data = explode(';base64,', $base64String);
            $data_type_aux = explode('image/', $data[0]);
            $extension = $data_type_aux[1];
            $file = base64_decode($data[1]);
            $fileName = Str::random() . ".$extension";
            $this->createFolder(public_path("storage/" . $folder));
            file_put_contents(public_path('storage/' . $folder . "/" . $fileName), $file);

            return [
                'path' => "storage/$folder/$fileName",
                'name' => $fileName,
                'type' => $this->getFileType($extension)
            ];
        } catch (Exception $exception) {
            Log::debug($exception);
            return false;
        }

    }

    protected function createFolder($path): void
    {
        if(!file_exists($path))
        {
            File::makeDirectory($path, 0755, true);
        }
    }

    protected function createFile($data): Model|Builder
    {
        return SimvebFile::query()->create($data);
    }

    protected function getFileType($extension) : string
    {
        $images = ['jpg','png','gif','jpeg','ico','webp'];

        return in_array(Str::lower($extension),$images) ? SimvebFile::IMAGE : SimvebFile::FILE;
    }
}
