<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mountain extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'village_id',
        'elevation_masl',
        'coordinates',
        'description',
        'is_open',
        'is_active',
        'closed_since',
        'length_km',
        'elevation_gain_m',
        'est_duration_minutes',
        'difficulty',
    ];

    protected function casts(): array
    {
        return [
            'is_open' => 'boolean',
            'is_active' => 'boolean',
            'closed_since' => 'date',
            'length_km' => 'float',
            'elevation_gain_m' => 'float',
            'est_duration_minutes' => 'float',
            'avg_rating' => 'float',
        ];
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    public function basecamps(): HasMany
    {
        return $this->hasMany(Basecamp::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(MountainImage::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(MountainRating::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}
