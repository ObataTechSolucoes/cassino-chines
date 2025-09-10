<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'event', 'module', 'target_type', 'target_id',
        'route', 'method', 'ip', 'user_agent',
        'request', 'before', 'after', 'message',
    ];

    protected $casts = [
        'request' => 'array',
        'before' => 'array',
        'after' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

