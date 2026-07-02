<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class MountainImage extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    const CREATED_AT = 'uploaded_at';

    protected $fillable = [
        'mountain_id',
        'image_url',
        'source_url',
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

    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (! $value) {
                    return asset('images/default-mountain.jpg');
                }

                if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://') || str_starts_with($value, '/')) {
                    return $value;
                }

                if (Storage::disk('public')->exists($value)) {
                    return asset('storage/' . $value);
                }

                return asset('images/default-mountain.jpg');
            },
        );
    }

    public function mountain(): BelongsTo
    {
        return $this->belongsTo(Mountain::class);
    }
}
