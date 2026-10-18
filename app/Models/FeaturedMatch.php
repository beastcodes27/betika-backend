<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class FeaturedMatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'bookmaker_id',
        'league',
        'kickoff',
        'venue',
        'home_team_name',
        'home_team_code',
        'home_team_logo',
        'home_team_color',
        'home_team_form',
        'home_odds',
        'away_team_name',
        'away_team_code',
        'away_team_logo',
        'away_team_color',
        'away_team_form',
        'away_odds',
        'draw_odds',
        'tipster_consensus',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'home_odds' => 'decimal:2',
            'away_odds' => 'decimal:2',
            'draw_odds' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function bookmaker(): BelongsTo
    {
        return $this->belongsTo(Bookmaker::class);
    }

    public function polls(): HasMany
    {
        return $this->hasMany(CommunityPoll::class);
    }

    public function activePoll(): HasOne
    {
        return $this->hasOne(CommunityPoll::class)->where('is_live', true)->latestOfMany();
    }
}
