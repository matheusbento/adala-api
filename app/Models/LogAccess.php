<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\LogAccess
 *
 * @property int $id
 * @property string $subject_type
 * @property int $subject_id
 * @property string $action_type
 * @property int|null $created_by_user_id
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model|\Eloquent $subject {@see \App\Models\LogAccess::subject()}
 * @property-read LaravelUser|null $user {@see \App\Models\LogAccess::user()}
 * @method static \Illuminate\Database\Eloquent\Builder|LogAccess[] whereActionType()
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|LogAccess newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LogAccess newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LogAccess query()
 */
class LogAccess extends Model
{
    protected $table = 'log_accesses';

    protected $fillable = [
        'parent_type',
        'parent_id',
        'user_id',
        'type',
    ];

    public const SILO_FOLDER_ATTRIBUTE_TYPE = 'silo_folder_attribute_type';
    public const CUBE_ITEM_VIEW_TYPE = 'cube_item_view_type';

    public function parent(): BelongsTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(LaravelUser::class, 'user_id')->withTrashed();
    }
}
