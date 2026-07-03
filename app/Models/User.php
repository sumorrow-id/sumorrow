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
use App\Models\Community;
use App\Notifications\ResetPasswordNotification;
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

    /**
     * Kirim notifikasi reset password menggunakan template kustom (Bahasa Indonesia).
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
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
        return $this->hasMany(Post::class, 'user_id');
    }

    public function postReplies(): HasMany
    {
        return $this->hasMany(PostReply::class, 'user_id');
    }

    public function communities() {
        return $this->belongsToMany(Community::class, 'community_user');
    }
    public function gears(): HasMany
    {
        return $this->hasMany(Gear::class);
    }

    public function followings(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'following_id')->withTimestamps();
    }

    public function followers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'following_id', 'follower_id')->withTimestamps();
    }

    public function isFollowing(User $user): bool
    {
        return $this->followings()->where('following_id', $user->id)->exists();
    }

    public function achievements() // BelongsToMany relationship
    {
        return $this->belongsToMany(Achievement::class)->withPivot('unlocked_at');
    }

    public function creator()
    {
        // Menambahkan 'created_by' sebagai foreign key ketiga
        return $this->belongsTo(User::class, 'created_by');
    }
}
