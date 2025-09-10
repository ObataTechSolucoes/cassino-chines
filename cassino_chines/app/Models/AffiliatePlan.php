<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AffiliatePlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'ggr_share',
        'rev_share',
        'cpa_amount',
        'cpa_ftd_min',
        'active',
    ];

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
