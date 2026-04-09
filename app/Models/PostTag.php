<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostTag extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'post_id',
        'keyword',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function setKeywordAttribute(string $value): void
    {
        $this->attributes['keyword'] = strtolower(trim($value));
    }
}
