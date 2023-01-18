<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TagResource extends JsonResource
{
    /**
     * @OA\Schema(
     *     schema="Tag",
     *     title="Tag",
     *     required={"id", "name"},
     *     @OA\Property(property="id", format="int64", description="Tag ID", example=1),
     *     @OA\Property(property="name", format="string", description="Tag name", example="Pickle"),
     * )
     */

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return array_merge(
            parent::toArray($request),
            [
                'related' => $this->whenLoaded('related'),
            ]
        );
    }
}
