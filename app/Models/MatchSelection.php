<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchSelection extends Model
{
    use HasFactory;

    protected $fillable = [
        'betslip_id',
        'match',
        'home_team',
        'away_team',
        'league',
        'kickoff',
        'pick',
        'odds',
        'confidence',
        'result',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'odds' => 'decimal:2',
            'confidence' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function betslip(): BelongsTo
    {
        return $this->belongsTo(Betslip::class);
    }
}
