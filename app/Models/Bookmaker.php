<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bookmaker extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'code_prefix',
        'logo_path',
        'brand_color',
        'website_url',
        'affiliate_url',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function betslips(): HasMany
    {
        return $this->hasMany(Betslip::class);
    }

    public function featuredMatches(): HasMany
    {
        return $this->hasMany(FeaturedMatch::class);
    }

    public function homeAds(): HasMany
    {
        return $this->hasMany(HomeAd::class);
    }
}
