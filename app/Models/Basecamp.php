<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Basecamp extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'mountain_id',
        'regency_id',
        'name',
        'base_elevation_masl',
        'length_km',
        'elevation_gain_m',
        'est_duration_minutes',
    ];

    protected function casts(): array
    {
        return [
            'length_km' => 'float',
        ];
    }

    public function mountain(): BelongsTo
    {
        return $this->belongsTo(Mountain::class);
    }

    public function regency(): BelongsTo
    {
        return $this->belongsTo(Regency::class);
    }
}
