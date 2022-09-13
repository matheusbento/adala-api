<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Propaganistas\LaravelFakeId\RoutesWithFakeIds;

class Cube extends Model
{
    use HasFactory;
    use RoutesWithFakeIds;

    protected $fillable = [
        'name',
        'description',
        'model',
        'user_id',
        'organization_id'
    ];

    protected $appends = ['fake_id'];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function getFakeIdAttribute() {
        return $this->getRouteKey();
    }
}
