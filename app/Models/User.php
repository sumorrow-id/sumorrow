<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\VerifyEmailNotification;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
        'username',
        'email',
        'google_id',
        'password_hash',
        // 'role' is intentionally omitted so it cannot be set via mass assignment (privilege escalation guard).
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

    public function getAuthPassword(): string
    {
        return (string) $this->password_hash;
    }

    /**
     * Resolve the avatar to a ready-to-use URL.
     *
     * `avatar_url` holds one of three shapes: a full external URL (Google /
     * ui-avatars), a root-relative path, or a public-disk-relative path from
     * an upload (e.g. "avatars/x.jpg"). Only the last needs the "storage/"
     * prefix — rendering it with a bare asset() (as several views did) 404s.
     * Centralise the resolution here so every caller renders the same URL.
     *
     * @param  string|null  $fallback  URL to use when no avatar is set; defaults
     *                                 to a generated initials avatar.
     */
    public function avatarUrl(?string $fallback = null): string
    {
        $value = $this->avatar_url;

        if ($value) {
            if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://') || str_starts_with($value, '/')) {
                return $value;
            }

            return asset('storage/'.$value);
        }

        return $fallback
            ?? 'https://ui-avatars.com/api/?name='.urlencode(mb_substr((string) $this->username, 0, 2)).'&background=random';
    }

    /**
     * @param  string  $token
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify((new ResetPasswordNotification($token))->locale(app()->getLocale()));
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify((new VerifyEmailNotification)->locale(app()->getLocale()));
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

    public function communities(): BelongsToMany
    {
        return $this->belongsToMany(Community::class);
    }

    public function gears(): HasMany
    {
        return $this->hasMany(Gear::class);
    }

    public function followings(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'following_id')->withTimestamps();
    }

    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'following_id', 'follower_id')->withTimestamps();
    }

    public function isFollowing(User $user): bool
    {
        return $this->followings()->where('following_id', $user->id)->exists();
    }

    public function achievements(): BelongsToMany
    {
        return $this->belongsToMany(Achievement::class)->withPivot('unlocked_at');
    }
}
