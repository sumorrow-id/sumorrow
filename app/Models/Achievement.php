<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;

class Achievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'icon_url',
        'tier',
    ];

    /**
     * Description in the active locale.
     *
     * Titles stay English in the database (AchievementService matches on them),
     * so translations are keyed by the snake_cased title — "First Summit"
     * becomes `achievements.first_summit`. Falls back to the stored English
     * description for any achievement without a translation.
     */
    public function localizedDescription(): string
    {
        $key = 'achievements.'.Str::snake($this->title);

        return Lang::has($key) ? __($key) : (string) $this->description;
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('unlocked_at');
    }
}
