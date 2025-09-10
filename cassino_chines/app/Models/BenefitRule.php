<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BenefitRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'benefit_id',
        'rule_type',
        'config',
    ];

    protected $casts = [
        'config' => 'array',
    ];

    public function benefit()
    {
        return $this->belongsTo(Benefit::class);
    }
}
