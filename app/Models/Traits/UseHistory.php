<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;
use App\Models\HistoryEntry;

/**
 * @property-read \App\Models\HistoryEntry[]|\Illuminate\Database\Eloquent\Collection $history {@see \App\Models\Traits\UseHistory::history()}
 * @mixin \Eloquent
 */
trait UseHistory
{
    public function history(): MorphMany
    {
        return $this->morphMany(HistoryEntry::class, 'subject');
    }

    public function addHistory(string $actionType, ?string $notes = null, bool $shouldSaveUser = true): Model
    {
        $attributes = [
            'action_type' => $actionType,
            'notes' => $notes,
        ];

        if ($shouldSaveUser && Auth::check()) {
            $attributes['created_by_user_id'] = Auth::id();
        }

        return $this->history()->create($attributes);
    }
}
