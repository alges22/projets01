<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->type->code,
            'description' => $this->description,
            'image_url' => $this->image_url,
            'color' => $this->color ?? '#FFFFFF',
            'children' => $this::collection($this->children),
        ];
    }
}
