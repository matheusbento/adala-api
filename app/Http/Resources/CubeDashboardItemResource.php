<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CubeDashboardItemResource extends JsonResource
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
            'name' => $this->name,
            'chart' => $this->chart,
            'processing_method' => $this->processing_method,
            'select' => $this->select,
            'filter' => $this->filter,
            'layout' => $this->layout,
        ];
    }
}
