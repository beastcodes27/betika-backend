<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Betslip extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipster_id',
        'bookmaker_id',
        'title',
        'slug',
        'category',
        'booking_code',
        'total_odds',
        'matches_count',
        'validity_time',
        'expires_at',
        'amount',
        'is_free',
        'confidence',
        'status',
        'views_count',
        'copies_count',
    ];

    protected function casts(): array
    {
        return [
            'total_odds' => 'decimal:2',
            'is_free' => 'boolean',
            'confidence' => 'integer',
            'matches_count' => 'integer',
            'views_count' => 'integer',
            'copies_count' => 'integer',
            'expires_at' => 'datetime',
        ];
    }

    public function tipster(): BelongsTo
    {
        return $this->belongsTo(TipsterProfile::class, 'tipster_id');
    }

    public function bookmaker(): BelongsTo
    {
        return $this->belongsTo(Bookmaker::class);
    }

    public function selections(): HasMany
    {
        return $this->hasMany(MatchSelection::class)->orderBy('sort_order');
    }

    public function purchasedSlips(): HasMany
    {
        return $this->hasMany(PurchasedSlip::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeFree(Builder $query): Builder
    {
        return $query->where('is_free', true);
    }

    public function scopeVip(Builder $query): Builder
    {
        return $query->where('category', 'VIP')->orWhere('is_free', false);
    }

    public function scopeCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }
}
