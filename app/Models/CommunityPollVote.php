<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunityPollVote extends Model
{
    use HasFactory;

    protected $fillable = [
        'featured_match_id',
        'user_id',
        'device_id',
        'vote_choice',
        'ip_address',
    ];

    public function featuredMatch(): BelongsTo
    {
        return $this->belongsTo(FeaturedMatch::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
