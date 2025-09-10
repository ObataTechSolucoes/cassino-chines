<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Gateway extends Model
{
    use HasFactory;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'gateways';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

        // CNPay - único gateway suportado
        'cnpay_uri',
        'cnpay_public_key',
        'cnpay_secret_key',
        'cnpay_webhook_url',

    ];

    protected $hidden = array('updated_at');

    /**
     * Get the user's first name.
     */

    /**
     * Proteger chave pública do CNPay em modo demo
     */
    protected function cnpayPublicKey(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => env('APP_DEMO') ? '*********************' : $value,
        );
    }

    /**
     * Proteger chave privada do CNPay em modo demo
     */
    protected function cnpaySecretKey(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => env('APP_DEMO') ? '*********************' : $value,
        );
    }
}
