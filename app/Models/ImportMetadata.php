<?php

namespace App\Models;

use App\Models\Casts\JsonCast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use ReflectionClass;

/**
 * Class ModelMetadata
 *
 * @mixin \Eloquent
 * @property int $id
 * @property int $model_id
 * @property string $model_type
 * @property mixed $raw_data
 * @property int $batch
 * @property string $file_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model|\Eloquent $model
 * @method static \Illuminate\Database\Eloquent\Builder|ImportMetadata newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ImportMetadata newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ImportMetadata query()
 * @method static \Illuminate\Database\Eloquent\Builder|ImportMetadata whereModel(\Illuminate\Database\Eloquent\Model|string $model)
 */
class ImportMetadata extends Model
{
    protected $table = 'baslake_imported_model_metadata';
    protected $fillable = [
        'raw_data',
        'batch',
        'file_name',
    ];

    protected $casts = [
        'raw_data' => JsonCast::class,
    ];

    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @param $query
     * @param Model|string $model
     * @return mixed
     * @throws \Exception|\ReflectionException
     */
    public function scopeWhereModel($query, $model)
    {
        if (is_object($model)) {
            $className = get_class($model);
        } else {
            $reflectionModelClass = new ReflectionClass($model);
            $className = $reflectionModelClass->getName();
        }
        return $query->where('model_type', $className);
    }

    /**
    * Get next batch number for especific model
    *
    * @param Model|string $model
    * @return int
    */
    public static function getNextBatchNumberFor($model): int
    {
        return (self::whereModel($model)->latest()->first()->batch ?? 0) + 1;
    }
}
