<?php

namespace App\Models\Traits;

use App\Models\LogAccess;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;

/**
 * @property-read \App\Models\LogAccess[]|\Illuminate\Database\Eloquent\Collection $history {@see \App\Models\Traits\HasLogAccess::history()}
 * @mixin \Eloquent
 */
trait HasLogAccess
{
    public function accesses(): MorphMany
    {
        return $this->morphMany(LogAccess::class, 'parent');
    }

    public function addLog(string $type): Model
    {
        $attributes['type'] = $type;
        $attributes['user_id'] = Auth::id();

        return $this->accesses()->create($attributes);
    }
}
