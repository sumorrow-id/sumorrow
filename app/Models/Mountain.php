<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mountain extends Model
{
    use HasFactory;

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
        'min_length_km',
        'max_length_km',
        'min_elevation_gain_m',
        'max_elevation_gain_m',
        'min_est_duration_minutes',
        'max_est_duration_minutes',
        'difficulty',
    ];

    protected function casts(): array
    {
        return [
            'is_open'                  => 'boolean',
            'is_active'                => 'boolean',
            'closed_since'             => 'date',
            'min_length_km'            => 'float',
            'max_length_km'            => 'float',
            'min_elevation_gain_m'     => 'float',
            'max_elevation_gain_m'     => 'float',
            'min_est_duration_minutes' => 'integer',
            'max_est_duration_minutes' => 'integer',
            'avg_rating'               => 'float',
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
