<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\UploadedFile;
use Propaganistas\LaravelFakeId\RoutesWithFakeIds;

class SiloFile extends File
{
    use HasFactory;
    use SoftDeletes;
    use RoutesWithFakeIds;

    public const FILE_TYPE = 'dataset';

    public const ACCEPTABLE_FILE_TYPES = [
        'text/plain',
        'text/csv',
        'application/octet-stream',
        'application/json'
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

    public function uploadFile(UploadedFile $file): array
    {
        return File::upload($file, "{$this->organization_id}/{$this->folder_id}/files");
    }

    public function getFakeIdAttribute()
    {
        return $this->getRouteKey();
    }
}
