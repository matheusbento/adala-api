<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Tag
 *
 * @property int $id
 * @property string $name
 * @method static \Database\Factories\TagFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tag query()
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Taggable[] $related
 * @property-read int|null $related_count
 */
class Tag extends Model
{
    use HasFactory;

    protected $table = 'baslake_tags';
    public $timestamps = false;

    protected $fillable = ['name'];

    public function related(): HasMany
    {
        return $this->hasMany(
            Taggable::class,
            'baslake_tag_id',
            'id',
            'baslake_model_tags',
        );
    }
}
