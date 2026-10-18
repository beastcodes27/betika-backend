<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchasedSlip extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'betslip_id',
        'amount_paid',
        'booking_code_unlocked',
        'status',
        'purchased_at',
    ];

    protected function casts(): array
    {
        return [
            'purchased_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function betslip(): BelongsTo
    {
        return $this->belongsTo(Betslip::class);
    }
}
