<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CubeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->fake_id,
            'identifier' => $this->identifier,
            'current_status' => $this->current_status,
            'name' => $this->name,
            'description' => $this->description,
            'is_dataflow' => $this->is_dataflow,
            'folders' => $this->whenLoaded('folders', SiloFolderResource::collection($this->folders)),
            'files' => $this->whenLoaded('files', SiloFileResource::collection($this->files)),
            'metadata' => $this->whenLoaded('metadata', CubeMetadataResource::collection($this->metadata)),
            'history' => $this->whenLoaded('history', HistoryEntryResource::collection($this->history)),
        ];
    }
}
