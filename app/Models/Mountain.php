<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mountain extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'elevation_masl',
        'coordinates',
        'description',
        'is_active',
        'closed_since',
        'difficulty',
    ];

    protected function casts(): array
    {
        return [
            'is_active'    => 'boolean',
            'closed_since' => 'date',
            'avg_rating'   => 'float',
        ];
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
