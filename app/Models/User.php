<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasUuids, Notifiable;

    public $incrementing = false;
    protected $keyType = 'string';
    public const UPDATED_AT = null;

    protected $fillable = [
        // 'id', dikomen aja biar ga dimanipulasi orang luar pake req form
        'username',
        'email',
        'google_id',
        'password_hash',
        'email_verified_at',
        'avatar_url',
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
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
        return ! is_null($this->email_verified_at) || ! is_null($this->google_id);
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
}
