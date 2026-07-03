<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'user_id',
        'community_id',
        'title',
        'description',
        'event_date',
        'location',
        'image_url'
    ];

    // Agar event_date otomatis dikenali sebagai objek tanggal
    protected $casts = [
        'event_date' => 'datetime',
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke Community
    public function community()
    {
        return $this->belongsTo(Community::class);
    }
}
