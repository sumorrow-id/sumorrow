<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'author_id',
        'mountain_id',
        'climbing_date',
        'duration_days',
        'title',
        'body',
        'category_tag',
        'gif_url',
    ];

    protected function casts(): array
    {
        return [
            'climbing_date' => 'date',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function mountain(): BelongsTo
    {
        return $this->belongsTo(Mountain::class, 'mountain_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(PostImage::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(PostReply::class);
    }

    public function tags(): HasMany
    {
        return $this->hasMany(PostTag::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(PostComment::class)->with('user')->oldest();
    }

    public function likes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'post_likes')->withTimestamps();
    }

    public function saves(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'post_saves')->withTimestamps();
    }

    /**
     * Scope to filter posts by a tag keyword.
     *
     * Usage: Post::byTag('hiking-stories')->latest()->paginate(10)
     */
    public function scopeByTag(Builder $query, string $keyword): Builder
    {
        return $query->whereHas('tags', function (Builder $sub) use ($keyword) {
            $sub->where('keyword', strtolower(trim($keyword)));
        });
    }
}
