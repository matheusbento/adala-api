<?php

namespace App\Models;

use App\Models\Casts\JsonCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiloFileAttributes extends Model
{
    use HasFactory;

    protected $table = 'silo_file_attributes';

    const TYPE_TABLE = "table";
    const TYPE_COMPLEX_KEY = "complex_key";

    protected $casts = [
        'attributes' => JsonCast::class,
    ];

    protected $fillable = [
        'type',
        'name',
        'attributes',
        'silo_file_id',
    ];

    public function siloFile(): BelongsTo
    {
        return $this->belongsTo(SiloFile::class);
    }
}
