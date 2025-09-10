<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Benefit extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'priority',
        'stacking_rules',
        'rollover',
        'cap',
        'conflicts',
    ];

    protected $casts = [
        'stacking_rules' => 'array',
        'conflicts' => 'array',
    ];

    public function rules()
    {
        return $this->hasMany(BenefitRule::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_benefits')
            ->withTimestamps()
            ->withPivot('status', 'rollover_progress', 'credited_at');
    }
}
