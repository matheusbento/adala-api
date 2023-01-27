<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SiloFileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if ($request->only_attributes) {
            return [
                'id' => $this->fake_id,
                'attributes' => $this->whenLoaded('attributes', fn () => SiloFileAttributeResource::collection($this->attributes)),
            ];
        }
        return [
            'id' => $this->fake_id,
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->current_status,
            $this->mergeWhen($this->relationLoaded('file'), [
                'file' => [
                    'id' => $this->file->id,
                    'mime' => $this->file->mime,
                    'size' => $this->file->size,
                    'name' => $this->file->original,
                    'original' => $this->file->original,
                ],
            ]),
            'created_at' => $this->created_at,
            'owner' => $this->whenLoaded('file', fn () => new UserResource($this->file->user)),
            'tags' => $this->whenLoaded('file', fn () => TagResource::collection($this->file->tags)),
            'attributes' => $this->whenLoaded('attributes', fn () => SiloFileAttributeResource::collection($this->attributes)),
        ];
    }
}
