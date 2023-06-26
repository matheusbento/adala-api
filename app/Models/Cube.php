<?php

namespace App\Models;

use App\Models\Traits\HasStatuses;
use App\Models\Traits\UseHistory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Propaganistas\LaravelFakeId\RoutesWithFakeIds;

class Cube extends Model
{
    use HasFactory;
    use RoutesWithFakeIds;
    use HasStatuses;
    use SoftDeletes;
    use UseHistory;

    protected ?string $currentStatusColumn = 'current_status';

    public const CREATING_STATUS = 'creating';
    public const READY_TO_ANALYSIS_STATUS = 'ready_to_analysis';
    public const CREATING_ERROR_STATUS = 'creating_error';
    public const INVALID_STATUS = 'invalid';

    public const CREATED_HISTORY_TYPE = 'cube_was_created';
    public const EDITED_HISTORY_TYPE = 'cube_was_edited';

    public const HISTORY_MESSAGES = [
        self::CREATED_HISTORY_TYPE => 'Cube was created',
        self::EDITED_HISTORY_TYPE => 'Cube was edited',
    ];

    protected $fillable = [
        'identifier',
        'name',
        'description',
        'user_id',
        'organization_id',
    ];

    public static function boot()
    {
        parent::boot();

        self::created(function ($model) {
            $model->generateIdentifier();
        });
    }

    protected $appends = ['fake_id'];

    public function generateIdentifier()
    {
        // TODO - Improve
        $this->update([
            'identifier' => Str::random(50),
        ]);
    }

    public function getFakeIdAttribute()
    {
        return $this->getRouteKey();
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function metadata(): HasMany
    {
        return $this->hasMany(CubeMetadata::class, 'cube_id');
    }

    public function files(): BelongsToMany
    {
        return $this->belongsToMany(SiloFile::class, 'cube_files', 'cube_id');
    }

    public function attributes(): MorphMany
    {
        return $this->morphMany(SiloFileAttributes::class, 'parent');
    }

    public function folders(): BelongsToMany
    {
        return $this->belongsToMany(SiloFolder::class, 'cube_folders', 'cube_id');
    }
}
