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
        'province_id',
        'name',
        'elevation_masl',
        'length_km',
        'elevation_gain_m',
        'coordinates',
        'description',
        'is_active',
        'closed_since',
        'difficulty',
        'avg_rating',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'closed_since' => 'date',
            'avg_rating' => 'float',
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

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    /**
     * Parse the stored DMS coordinate string ("8 deg 16' 0\" S, 115 deg 25' 0\" E")
     * into decimal degrees, or null when it isn't parseable. Same DMS grammar as
     * the admin coordinate picker and WeatherController.
     *
     * @return array{lat: float, lng: float}|null
     */
    public function coordinatesToDecimal(): ?array
    {
        $parts = explode(',', (string) $this->coordinates);
        if (count($parts) < 2) {
            return null;
        }

        $dmsToDecimal = function (string $dms): ?float {
            if (preg_match('/(\d+)\s*deg\s*(\d+)\s*\'\s*(\d+(?:\.\d+)?)\s*"\s*([NSEW])/i', trim($dms), $m)) {
                $decimal = (float) $m[1] + (float) $m[2] / 60 + (float) $m[3] / 3600;

                return in_array(strtoupper($m[4]), ['S', 'W'], true) ? -$decimal : $decimal;
            }

            return null;
        };

        $lat = $dmsToDecimal($parts[0]);
        $lng = $dmsToDecimal($parts[1]);

        return ($lat === null || $lng === null) ? null : ['lat' => $lat, 'lng' => $lng];
    }

    /**
     * Great-circle (haversine) distance in kilometres from the given point to
     * this mountain, or null when the mountain has no parseable coordinates.
     */
    public function distanceKmFrom(float $latitude, float $longitude): ?float
    {
        $coordinates = $this->coordinatesToDecimal();
        if ($coordinates === null) {
            return null;
        }

        $earthRadiusKm = 6371.0;
        $latDelta = deg2rad($coordinates['lat'] - $latitude);
        $lngDelta = deg2rad($coordinates['lng'] - $longitude);

        $a = sin($latDelta / 2) ** 2
            + cos(deg2rad($latitude)) * cos(deg2rad($coordinates['lat'])) * sin($lngDelta / 2) ** 2;

        return $earthRadiusKm * 2 * asin(min(1.0, sqrt($a)));
    }
}
