<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Propaganistas\LaravelFakeId\RoutesWithFakeIds;

class Organization extends Model
{
    use HasFactory;
    use RoutesWithFakeIds;

    public static function boot()
    {
        parent::boot();

        self::created(function($model){
            $this->folders()->create([
                'name' => "Default Folder",
                'description' => "Default",
                'owner_id' => $this->owner->id,
            ]);
        });
    }

    protected $fillable = [
        'name',
        'description',
        'owner_id',
    ];

    protected $appends = ['fake_id'];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function getFakeIdAttribute() {
        return $this->getRouteKey();
    }

    public function cubes(): HasMany
    {
        return $this->hasMany(Cube::class, 'organization_id');
    }

    public function scopeWhereIAmOwner($query)
    {
        $query->where('owner_id', Auth::user()->id);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_organizations', 'organization_id', 'user_id');
    }

    public function folders(): HasMany
    {
        return $this->hasMany(SiloFolder::class);
    }
}
