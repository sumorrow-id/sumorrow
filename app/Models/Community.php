<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Community extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'slug',
        'privacy',
        'image_url',
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
}
