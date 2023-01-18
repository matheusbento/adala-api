<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CubeFullResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'model' => $this->model,
            'metadata' => $this->whenLoaded('metadata'),
        ];
    }
}
