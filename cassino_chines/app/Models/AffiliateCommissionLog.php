<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliateCommissionLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'affiliate_user_id',
        'referred_user_id',
        'period',
        'calc_type',
        'base_amount',
        'commission_amount',
        'status',
        'error_reason',
        'idempotency_key',
    ];

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(User::class, 'affiliate_user_id');
    }

    public function referred(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_user_id');
    }
}
