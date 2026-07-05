<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Community extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'slug',
        'privacy',
        'join_token',
        'image_url',
        'banner_url',
        'created_by',
    ];

    /**
     * Get the user who created this community
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the members of this community
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'community_user')
            ->withPivot('role', 'joined_at');
    }

    /**
     * Check if a user is a member of this community
     */
    public function isMember(User $user): bool
    {
        return $this->members()->where('user_id', $user->id)->exists();
    }

    /**
     * Get member count
     */
    public function getMemberCount(): int
    {
        return $this->members()->count();
    }

    /**
     * Get the forum posts made inside this community
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Get the events scheduled in this community
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Check if a user created this community (may edit/delete it)
     */
    public function isCreatedBy(?User $user): bool
    {
        return $user !== null && $this->created_by === $user->id;
    }

    /**
     * Profile image URL, falling back to the bundled default
     */
    public function profileImageUrl(): string
    {
        return asset($this->image_url ?: 'images/community/profile.avif');
    }

    /**
     * Banner image URL, falling back to the bundled default
     */
    public function bannerImageUrl(): string
    {
        return asset($this->banner_url ?: 'images/community/banner.jpg');
    }
}
