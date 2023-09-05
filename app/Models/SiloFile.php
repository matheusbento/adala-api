<?php

namespace App\Models;

use App\Models\Traits\HasStatuses;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\UploadedFile;
use Propaganistas\LaravelFakeId\RoutesWithFakeIds;

class SiloFile extends File
{
    use HasFactory;
    use SoftDeletes;
    use RoutesWithFakeIds;
    use HasStatuses;

    protected ?string $currentStatusColumn = 'current_status';

    public const CREATED_FILE_STATUS = 'created';
    public const PRE_PROCESSING_STATUS = 'pre_processing';
    public const READY_FOR_USE_STATUS = 'ready_for_use';
    public const PROCESSING_STATUS = 'processing';
    public const PROCESSING_ERROR_STATUS = 'processing_error';
    public const INVALID_STATUS = 'invalid';

    public const FILE_TYPE = 'dataset';

    public const ACCEPTABLE_FILE_TYPES = [
        'text/plain',
        'text/csv',
        'image/fits',
        'image/fit',
        'application/octet-stream',
        'application/json',
        'application/vnd.ms-excel',
        'application/csv',
        '',
    ];

    protected $table = 'silo_files';

    protected $fillable = [
        'description',
        'name',
        'folder_id',
    ];

    protected $appends = ['fake_id'];

    public function folder(): BelongsTo
    {
        return $this->belongsTo(SiloFolder::class);
    }

    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }

    public function attributes(): MorphMany
    {
        return $this->morphMany(SiloFileAttributes::class, 'parent');
    }

    public function uploadFile(UploadedFile $file): array
    {
        return File::upload($file, "{$this->organization_id}/{$this->folder_id}/files");
    }

    public function getFakeIdAttribute()
    {
        return $this->getRouteKey();
    }
}
