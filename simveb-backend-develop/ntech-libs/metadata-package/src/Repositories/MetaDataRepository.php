<?php

namespace Ntech\MetadataPackage\Repositories;

use Ntech\MetadataPackage\Models\MetaData;

class MetaDataRepository
{
    public function getList()
    {
        return MetaData::query()->filter()->latest()->get();
    }

    /**
     * @param array $metaData
     * @return void
     */
    function update(array $metaData): mixed
    {
        MetaData::query()
            ->where('name', $metaData['name'])
            ->update(["data" => $metaData]);

        return $metaData;
    }

    public function getMetaDataByName($name)
    {
        return json_decode(json_encode(MetaData::query()
            ->where("name", $name)
            ->firstOrFail()
            ->data));
    }
}
