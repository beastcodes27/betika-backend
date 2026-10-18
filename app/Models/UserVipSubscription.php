<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserVipSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vip_plan_id',
        'tipster_id',
        'started_at',
        'expires_at',
        'amount_paid',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'expires_at' => 'datetime',
            'amount_paid' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(VipPlan::class, 'vip_plan_id');
    }

    public function tipster(): BelongsTo
    {
        return $this->belongsTo(TipsterProfile::class, 'tipster_id');
    }

    public function isValid(): bool
    {
        return $this->status === 'active' && $this->expires_at->isFuture();
    }
}
