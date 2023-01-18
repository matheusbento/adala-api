<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * Class ModelStatus
 *
 * @package App\Models
 * @property int $id
 * @property int $model_id
 * @property string $model_type
 * @property string $type
 * @property string $status
 * @property string $reason
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|BaslakeModelStatus whereType(string $type) {@see BaslakeModelStatus::scopeWhereType()}
 * @property-read Model|\Eloquent $model
 * @method static Builder|BaslakeModelStatus newModelQuery()
 * @method static Builder|BaslakeModelStatus newQuery()
 * @method static Builder|BaslakeModelStatus query()
 * @mixin \Eloquent
 */
class BaslakeModelStatus extends Model
{
    protected $table = 'baslake_model_statuses';
    protected $fillable = [
        'type',
        'status',
        'reason',
    ];

    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeWhereType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }
}
