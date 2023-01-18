<?php

namespace App\Models;

use App\Models\LaravelUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\HistoryEntry
 *
 * @property int $id
 * @property string $subject_type
 * @property int $subject_id
 * @property string $action_type
 * @property int|null $created_by_user_id
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model|\Eloquent $subject {@see \App\Models\HistoryEntry::subject()}
 * @property-read LaravelUser|null $user {@see \App\Models\HistoryEntry::user()}
 * @method static \Illuminate\Database\Eloquent\Builder|HistoryEntry[] whereActionType()
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|HistoryEntry newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HistoryEntry newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HistoryEntry query()
 */
class HistoryEntry extends Model
{
    protected $table = 'baslake_history_entries';

    protected $fillable = [
        'subject_type',
        'subject_id',
        'action_type',
        'notes',
        'created_by_user_id',
    ];

    public function subject(): BelongsTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(LaravelUser::class, 'created_by_user_id')->withTrashed();
    }
}
