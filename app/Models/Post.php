<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;

    public const CATEGORY_TAGS = [
        'Hiking Stories',
        'Tips and Trick',
        'Gear & Equipment',
        'Safety & Survival',
    ];

    protected $fillable = [
        'author_id',
        'community_id',
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

    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(PostImage::class);
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

    /**
     * Render the post body markdown to HTML with raw HTML stripped, so a
     * user-authored body can't inject <script> or unsafe links. Callers use
     * {!! !!}, so the sanitising must happen here, not in the view.
     */
    public function renderedBody(): string
    {
        return Str::markdown($this->body ?? '', [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
    }
}
