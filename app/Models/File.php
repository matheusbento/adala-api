<?php

namespace App\Models;

use App\Models\Traits\HasTags;
use Aws\CloudFront\CloudFrontClient;
use Aws\Exception\AwsException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File as FileFacade;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * App\Models\File
 *
 * @property int $id
 * @property string $owner_type
 * @property int $owner_id
 * @property int $created_by_user_id
 * @property string $file_type
 * @property string $drive
 * @property string $url
 * @property string $path
 * @property string $original
 * @property string $mime
 * @property int $size
 * @property int|null $sort_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tag[] $tags
 * @property-read int|null $tags_count
 * @property-read LaravelUser|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|File newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|File newQuery()
 * @method static \Illuminate\Database\Query\Builder|File onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|File query()
 * @method static \Illuminate\Database\Query\Builder|File withTrashed()
 * @method static \Illuminate\Database\Query\Builder|File withoutTrashed()
 * @property-read Model|\Eloquent $owner
 */
class File extends Model
{
    use HasTags;
    use SoftDeletes;

    public const PUBLIC_DISK = 's3_public';

    protected $fillable = [
        'owner_type',
        'owner_id',
        'created_by_user_id',
        'file_type',
        'drive',
        'url',
        'path',
        'original',
        'mime',
        'size',
        'sort_order',
    ];

    public static function boot(): void
    {
        parent::boot();

        static::deleted(function ($model) {
            if (App::isProduction() && !$model->trashed()) {
                Storage::disk($model->drive)->delete($model->path);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    public static function upload(UploadedFile $file, string $path = 'uploads', ?string $disk = null): array
    {
        $disk = $disk === null ? config('filesystems.cloud') : $disk;
        $folder = preg_replace('/\/$/', '', config('filesystems.disks.' . $disk . '.folder'));
        $file_path = Storage::disk($disk)->put($folder . '/' . $path, $file);

        return [
            'drive' => $disk,
            'url' => Storage::disk($disk)->url($file_path),
            'path' => $file_path,
            'original' => $file->getClientOriginalName(),
            'mime' => $file->getMimeType(),
            'size' => $file->getSize(),
        ];
    }

    public static function getOriginalsByOwner(string $owner_type, int $owner_id, ?array $file_types = null): array
    {
        $query = self::query()->select('original')
            ->where('owner_type', $owner_type)
            ->where('owner_id', $owner_id);

        if ($file_types && count($file_types)) {
            $query->whereIn('file_type', $file_types);
        }

        return $query->pluck('original')
            ->toArray();
    }

    public static function getSignedUrl(string $path, int $expires_in = 3600, $api = true)
    {
        try {
            $cloudFrontClient = new CloudFrontClient([
                'profile' => 'default',
                'version' => '2014-11-06',
                'region' => config('filesystems.disks.s3.cloudfront_region'),
            ]);

            $result = $cloudFrontClient->getSignedUrl([
                'url' => config('filesystems.disks.s3.cloudfront_signed_url') . '/' . $path,
                'expires' => time() + $expires_in,
                'private_key' => storage_path('s3-private-key.pem'),
                'key_pair_id' => config('filesystems.disks.s3.key_pair'),
            ]);

            if ($api) {
                return response()
                    ->json(['signedUrl' => $result])
                    ->header('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
            }
            return $result;
        } catch (AwsException $e) {
            return response()->json(
                [
                    'message' => $e->getAwsErrorMessage(),
                ],
                $e->getStatusCode()
            );
        }
    }

    public static function download(string $path, ?string $disk = null): StreamedResponse
    {
        $disk = $disk === null ? config('filesystems.cloud') : $disk;
        return Storage::disk($disk)->download($path);
    }

    public static function read(string $path, ?string $disk = null)
    {
        $disk = $disk === null ? config('filesystems.cloud') : $disk;
        \Log::info([$disk, $path]);
        return Storage::disk($disk)->get($path);
    }

    public static function downloadZipped(array $files, ?string $disk = null): BinaryFileResponse
    {
        $disk = $disk === null ? config('filesystems.cloud') : $disk;

        $zip_path = storage_path('app/tmp');
        FileFacade::isDirectory($zip_path) or FileFacade::makeDirectory($zip_path, 0755, true, true);

        $zip_file = storage_path('app/tmp/worker_documents-' . md5(uniqid()) . '.zip');
        $zip = new Filesystem(new ZipArchiveAdapter($zip_file));

        foreach ($files as $file) {
            $zip->put($file->attributes['original'], Storage::disk($disk)->get($file->attributes['path']));
        }

        $zip->getAdapter()->getArchive()->close();

        return response()->download($zip_file)->deleteFileAfterSend(true);
    }
}
