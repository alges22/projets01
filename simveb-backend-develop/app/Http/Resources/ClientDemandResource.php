<?php

namespace App\Http\Resources;

use App\Traits\FormatFilesTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientDemandResource extends JsonResource
{
    use FormatFilesTrait;
    private string $source;
    public function __construct($resource, $source = 'database')
    {
        parent::__construct($resource);
        $this->source = $source;
    }
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'date' => $this->created_at,
            'created_at' => $this->created_at,
            'reference' => $this->reference,
            'status' => $this->status,
            'status_label' => $this->statusLabel,
            'service_id' => $this->service_id,
            'service_code' => $this->service->code,
            'service' => $this->service->name,
            'files' => $this->formatFiles($this->files),
            'steps' => getDemandSteps($this->resource),
            'is_editable' => $this->is_editable
        ];
    }
}
