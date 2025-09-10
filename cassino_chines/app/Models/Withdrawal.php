<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class
Withdrawal extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 0;
    public const STATUS_REVIEW = 1;
    public const STATUS_APPROVED = 2;
    public const STATUS_DENIED = 3;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'withdrawals';
    protected $appends = ['dateHumanReadable', 'createdAt'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'payment_id',
        'user_id',
        'amount',
        'type',
        'bank_info',
        'type',
        'proof',
        'pix_key',
        'pix_type',
        'currency',
        'symbol',
        'status',
        'cpf',
        'gateway',
        'transaction_id',
        'webhook_data',
        'review_notes',
        'denial_reason',
        'review_attachment',
        'reviewed_at',
    ];

    protected $casts = [
        'status' => 'integer',
        'reviewed_at' => 'datetime',
    ];


    /**
     * @return mixed
     */
    public function getCreatedAtAttribute()
    {
        return Carbon::parse($this->attributes['created_at']);
    }

    /**
     * @return mixed
     */
    public function getDateHumanReadableAttribute()
    {
        return Carbon::parse($this->created_at)->diffForHumans();
    }

    /**
     * @return BelongsTo
     */
    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
