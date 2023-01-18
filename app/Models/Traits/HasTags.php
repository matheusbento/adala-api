<?php

namespace App\Models\Traits;

use App\Models\Tag as ModelsTag;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * App\Models\Traits\HasTags
 *
 * @property-read \App\Models\Tag[]|\Illuminate\Database\Eloquent\Collection $tags
 * @mixin \Eloquent
 */
trait HasTags
{
    public function tags(): MorphToMany
    {
        return $this->morphToMany(
            ModelsTag::class,
            'model',
            'baslake_model_tags',
            null,
            'baslake_tag_id',
        );
    }
}
