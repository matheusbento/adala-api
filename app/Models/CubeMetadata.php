<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CubeMetadata extends Model
{
    protected $table = 'cube_metadatas';
    
    use HasFactory;

    protected $fillable = [
        'id',
        'field',
        'value',
        'cube_id',
    ];

    public function cube(): BelongsTo
    {
        return $this->belongsTo(Cube::class, 'cube_id');
    }
}
