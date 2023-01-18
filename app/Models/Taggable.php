<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * App\Models\Tag
 *
 * @property int $id
 * @property string $name
 * @method static \Illuminate\Database\Eloquent\Builder|Tag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tag query()
 * @mixin \Eloquent
 * @property int $baslake_tag_id
 * @property string $model_type
 * @property int $model_id
 * @property-read Model|\Eloquent $model
 */
class Taggable extends Model
{
    protected $table = 'baslake_model_tags';
    public $timestamps = false;

    protected $fillable = ['model_type', 'model_id'];

    public function model(): MorphTo
    {
        return $this->morphTo();
    }
}
