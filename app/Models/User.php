<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasUuids, Notifiable;

    public $incrementing = false;

    protected $keyType = 'string';

    public const UPDATED_AT = null;

    protected $fillable = [
        // 'id', dikomen aja biar ga dimanipulasi orang luar pake req form
        'username',
        'email',
        'google_id',
        'password_hash',
        'role',
        'created_at',
        'avatar_url',
        'cover_url',
        'bio',
        'email_verified_at',
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'email_verified_at' => 'datetime',
            'password_hash' => 'hashed',
        ];
    }

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function hasVerifiedEmail()
    {
        return ! is_null($this->created_at) || ! is_null($this->google_id);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(MountainRating::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'author_id');
    }

    public function postReplies(): HasMany
    {
        return $this->hasMany(PostReply::class, 'author_id');
    }

    public function gears(): HasMany
    {
        return $this->hasMany(Gear::class);
    }

    public function achievements() // BelongsToMany relationship
    {
        return $this->belongsToMany(Achievement::class)->withPivot('unlocked_at');
    }
}
