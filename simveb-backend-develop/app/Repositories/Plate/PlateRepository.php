<?php

namespace App\Repositories\Plate;

use App\Consts\Roles;
use App\Enums\ProfileTypesEnum;
use App\Jobs\ImportNonOrderedPlatesJob;
use App\Models\Plate;
use App\Traits\UploadFile;
use App\Traits\UserDataTrait;

class PlateRepository
{
    use UserDataTrait, UploadFile;

    private $class;

    public function __construct()
    {
        $this->class = Plate::class;
    }

    public function getAll(bool $paginate = true, $relations = [])
    {
        $query = $this->class::filter()->with($relations)->orderByDesc('created_at')->whereNull('rfid');

        $onlineProfile = getOnlineProfile();
        if ($onlineProfile->type->code != ProfileTypesEnum::anatt->name) {
            $query->where('institution_id', $onlineProfile->institution_id);
        } else {
            $query->whereNull('institution_id');
        }

        return $paginate ? $query->paginate(request('per_page', 15)) : $query->get();
    }

    public function store(array $data)
    {
        $filePath = $this->saveFile($data['file'], 'to-import/anatt-plates');

        ImportNonOrderedPlatesJob::dispatch($filePath['path'], getOnlineProfile());

        return ['message' => 'Enregistrement des plaques en cours!'];
    }

    public function stats()
    {
        $user = $this->user();

        if ($user->hasRole(Roles::SPACE_MEMBER) || $user->hasRole(Roles::SPACE_ADMIN) || $user->hasRole(Roles::SPACE_HEADER)) {

            $spaceId = $user->identity->space()->id;

            return [
                'in_stock' => Plate::where('space_id', $spaceId)->where('in_stock', false)->where('in_space_stock', true)->count(),
                'ordered' => Plate::where('space_id', $spaceId)->count(),
                'delivered_to_applicant' => Plate::where('space_id', $spaceId)->where('in_stock', false)->where('in_space_stock', false)->count(),
            ];
        } elseif ($user->hasRole(Roles::ADMIN)) { //use anatt member role
            return [
                'total' => Plate::count(),
                'in_stock' => Plate::where('in_stock', true)->count(),
                'ordered' => Plate::where('in_stock', false)->count(),
                'delivered_to_applicant' => Plate::where('in_stock', false)->where('in_space_stock', false)->count(),
            ];
        }

        return [];
    }

    public function getTotalPlates()
    {
        return [
            'plates_total' => $this->class::query()->filter()->count()
        ];
    }
}
