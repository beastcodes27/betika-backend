<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommunityPoll extends Model
{
    use HasFactory;

    protected $fillable = [
        'featured_match_id',
        'home_votes_count',
        'draw_votes_count',
        'away_votes_count',
        'total_votes_count',
        'is_live',
    ];

    protected function casts(): array
    {
        return [
            'home_votes_count' => 'integer',
            'draw_votes_count' => 'integer',
            'away_votes_count' => 'integer',
            'total_votes_count' => 'integer',
            'is_live' => 'boolean',
        ];
    }

    public function featuredMatch(): BelongsTo
    {
        return $this->belongsTo(FeaturedMatch::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(CommunityPollVote::class);
    }

    public function getPctHomeAttribute(): int
    {
        if ($this->total_votes_count <= 0) return 0;
        return (int) round(($this->home_votes_count / $this->total_votes_count) * 100);
    }

    public function getPctDrawAttribute(): int
    {
        if ($this->total_votes_count <= 0) return 0;
        return (int) round(($this->draw_votes_count / $this->total_votes_count) * 100);
    }

    public function getPctAwayAttribute(): int
    {
        if ($this->total_votes_count <= 0) return 0;
        return 100 - $this->pct_home - $this->pct_draw;
    }
}
