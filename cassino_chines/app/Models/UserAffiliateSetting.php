<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAffiliateSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'affiliate_plan_id',
        'override_type',
        'override_ggr_share',
        'override_rev_share',
        'override_cpa_amount',
        'note',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(AffiliatePlan::class, 'affiliate_plan_id');
    }
}
