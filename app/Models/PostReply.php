<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

class PostReply extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'post_id',
        'author_id',
        'parent_reply_id',
        'content',
    ];

    protected static function booted(): void
    {
        static::saving(function (PostReply $reply): void {
            if ($reply->parent_reply_id === null) {
                return;
            }

            $parent = PostReply::query()->find($reply->parent_reply_id);

            if ($parent === null) {
                throw ValidationException::withMessages([
                    'parent_reply_id' => 'Parent reply does not exist.',
                ]);
            }

            if ($parent->post_id !== $reply->post_id) {
                throw ValidationException::withMessages([
                    'parent_reply_id' => 'Parent reply must belong to the same post.',
                ]);
            }

            if ($parent->parent_reply_id !== null) {
                throw ValidationException::withMessages([
                    'parent_reply_id' => 'Replies can only be nested up to 2 levels.',
                ]);
            }
        });
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function parentReply(): BelongsTo
    {
        return $this->belongsTo(PostReply::class, 'parent_reply_id');
    }

    public function childrenReplies(): HasMany
    {
        return $this->hasMany(PostReply::class, 'parent_reply_id');
    }
}
