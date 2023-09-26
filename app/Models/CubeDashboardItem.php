<?php

namespace App\Models;

use App\Models\Casts\JsonCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CubeDashboardItem extends Model
{
    use HasFactory;

    protected $table = 'cube_dashboard_items';

    protected $casts = [
        'select' => JsonCast::class,
        'filter' => JsonCast::class,
        'layout' => JsonCast::class,
    ];

    protected $fillable = [
        'cube_id',
        'name',
        'chart',
        'processing_method',
        'select',
        'filter',
        'layout',
    ];

    public function cube(): BelongsTo
    {
        return $this->belongsTo(Cube::class, 'cube_id');
    }
}
