<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBenefit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'benefit_id',
        'status',
        'rollover_progress',
        'credited_at',
    ];

    protected $casts = [
        'credited_at' => 'datetime',
    ];

    public function benefit()
    {
        return $this->belongsTo(Benefit::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
