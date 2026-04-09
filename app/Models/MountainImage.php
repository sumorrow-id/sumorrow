<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MountainImage extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    const CREATED_AT = 'uploaded_at';

    protected $fillable = [
        'mountain_id',
        'image_url',
        'position',
        'is_cover',
        'uploaded_at',
    ];

    protected function casts(): array
    {
        return [
            'is_cover' => 'boolean',
            'uploaded_at' => 'datetime',
        ];
    }

    public function mountain(): BelongsTo
    {
        return $this->belongsTo(Mountain::class);
    }
}
