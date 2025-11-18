<?php

namespace Ntech\MetadataPackage\Traits;

use Ntech\MetadataPackage\Models\MetaData;

trait MetaDataTrait
{

    protected function getMetadataByName($name)
    {
        $data =  json_decode(json_encode(MetaData::query()
            ->where("name",$name)
            ->first()->data));

        $formattedData = [];

        foreach ($data as $datum){
            $formattedData[$datum->key] = $datum->value;
        }

        return json_decode(json_encode($formattedData));
    }
}
