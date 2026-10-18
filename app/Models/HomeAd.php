<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HomeAd extends Model
{
    use HasFactory;

    protected $fillable = [
        'bookmaker_id',
        'badge',
        'title',
        'subtitle',
        'highlight',
        'cta_text',
        'promo_code',
        'target_url',
        'is_active',
        'sort_order',
        'scroll_duration_seconds',
        'clicks_count',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'scroll_duration_seconds' => 'integer',
            'clicks_count' => 'integer',
        ];
    }

    public function bookmaker(): BelongsTo
    {
        return $this->belongsTo(Bookmaker::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
