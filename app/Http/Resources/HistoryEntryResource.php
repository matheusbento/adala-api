<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \Modules\Vms\Models\HistoryEntry
 */
class HistoryEntryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'subject_id' => $this->subject_id,
            'action_type' => $this->action_type,
            'user' => $this->whenLoaded('user'),
            'notes' => $this->notes,
            'created_at' => $this->created_at,
        ];
    }
}
