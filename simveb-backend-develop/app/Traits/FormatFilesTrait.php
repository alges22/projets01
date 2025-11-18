<?php

namespace App\Traits;

trait FormatFilesTrait
{
    protected function formatFiles($filesDoc)
    {
        $files = [];
        foreach ($filesDoc as $file){
            $files[] = [
                'url' => asset($file->path['path']),
                'name' => $file->fileType?->description,
                'type' => $file->type,
                'id' => $file->id
            ];
        }

        return $files;
    }
}
