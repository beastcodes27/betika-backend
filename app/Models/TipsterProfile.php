<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipsterProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'handle',
        'display_name',
        'bio',
        'specialty',
        'avatar_bg',
        'win_rate',
        'badge',
        'streak',
        'followers_count',
        'is_verified',
        'can_publish',
        'status',
        'approval_notes',
        'approved_by',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'win_rate' => 'decimal:2',
            'is_verified' => 'boolean',
            'can_publish' => 'boolean',
            'approved_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function betslips(): HasMany
    {
        return $this->hasMany(Betslip::class, 'tipster_id');
    }

    public function activeBetslips(): HasMany
    {
        return $this->hasMany(Betslip::class, 'tipster_id')->where('status', 'active');
    }
}
