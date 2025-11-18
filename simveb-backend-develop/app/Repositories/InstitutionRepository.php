<?php
namespace App\Repositories;

use App\Models\Institution\Institution;
use App\Traits\UploadFile;
use Illuminate\Support\Arr;

class InstitutionRepository
{
    use UploadFile;

    public function store(array $data)
    {
        $logoData = Arr::pull($data, 'logo');

        if ($logoData) {
            if ($filePath = $this->saveFile($logoData, 'institution_logo')) {
                $data['logo_path'] = $filePath;
            }
        }

        $institution = Institution::create($data);

        return $institution;
    }

    public function update(Institution $institution, array $data)
    {
        $logoData = Arr::pull($data, 'logo');

        if ($logoData) {
            if ($filePath = $this->saveFile($logoData, 'institution_logo')) {
                $data['logo_path'] = $filePath;
            }
        }

        $institution->update($data);

        return $institution;
    }
}
