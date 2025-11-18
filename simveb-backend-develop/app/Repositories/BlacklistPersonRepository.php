<?php

namespace App\Repositories;

use App\Jobs\ImportBlacklistPersonJob;
use App\Traits\UploadFile;

class BlacklistPersonRepository
{
    use UploadFile;

    public function import(array $data)
    {
        $filePath = $this->saveFile($data['file'], 'to-import/blacklist-persons');

        ImportBlacklistPersonJob::dispatch($filePath['path'], getOnlineProfile());

        return ['message' => 'Enregistrement des personnes sur la liste noire en cours!'];
    }
}
