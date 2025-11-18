<?php

namespace App\Http\Resources\Vehicle;

use App\Models\Config\Park;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class VehiclePassageCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
          'data' => $this->collection->transform(function ($passage) {
              return new VehiclePassageResource($passage);
          })->toArray()
        ];
    }
}
