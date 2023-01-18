<?php

namespace App\Models;

use App\Models\Casts\JsonCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Propaganistas\LaravelFakeId\RoutesWithFakeIds;

class Cube extends Model
{
    use HasFactory;
    use RoutesWithFakeIds;

    protected $fillable = [
        'identifier',
        'name',
        'description',
        'model',
        'user_id',
        'organization_id',
    ];

    protected $casts = [
        'model' => JsonCast::class,
    ];

    protected $appends = ['fake_id'];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function getFakeIdAttribute()
    {
        return $this->getRouteKey();
    }

    public function metadata(): HasMany
    {
        return $this->hasMany(CubeMetadata::class, 'cube_id');
    }
}
