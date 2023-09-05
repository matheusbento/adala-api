<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Propaganistas\LaravelFakeId\RoutesWithFakeIds;

class SiloFolder extends Model
{
    use HasFactory;
    use SoftDeletes;
    use RoutesWithFakeIds;

    protected $table = 'silo_folders';

    protected $fillable = [
        'description',
        'name',
        'owner_id',
        'organization_id',
        'category_id',
        'is_dataflow',
    ];

    protected $appends = ['fake_id'];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(SiloFile::class, 'folder_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getFakeIdAttribute()
    {
        return $this->getRouteKey();
    }
}
