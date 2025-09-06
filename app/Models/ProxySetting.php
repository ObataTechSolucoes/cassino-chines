<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProxySetting extends Model
{
    use HasFactory;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'proxy_settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'proxy_enabled',
        'proxy_host',
        'proxy_port',
        'proxy_username',
        'proxy_password',
        'proxy_type',
        'proxy_verify_ssl',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'proxy_enabled' => 'boolean',
        'proxy_port' => 'integer',
        'proxy_verify_ssl' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the active proxy configuration
     *
     * @return static|null
     */
    public static function getActive()
    {
        return static::where('proxy_enabled', true)->first();
    }

    /**
     * Check if proxy is enabled
     *
     * @return bool
     */
    public static function isEnabled()
    {
        return static::where('proxy_enabled', true)->exists();
    }

    /**
     * Get proxy configuration for HTTP client
     *
     * @return array
     */
    public function getHttpClientConfig()
    {
        if (!$this->proxy_enabled || !$this->proxy_host) {
            return [];
        }

        $config = [
            'proxy' => $this->proxy_type . '://' . $this->proxy_host . ':' . $this->proxy_port,
        ];

        if ($this->proxy_username && $this->proxy_password) {
            $config['proxy'] = $this->proxy_type . '://' . $this->proxy_username . ':' . $this->proxy_password . '@' . $this->proxy_host . ':' . $this->proxy_port;
        }

        if (!$this->proxy_verify_ssl) {
            $config['verify'] = false;
        }

        return $config;
    }

    /**
     * Get proxy URL for external services
     *
     * @return string|null
     */
    public function getProxyUrl()
    {
        if (!$this->proxy_enabled || !$this->proxy_host) {
            return null;
        }

        $url = $this->proxy_type . '://';
        
        if ($this->proxy_username && $this->proxy_password) {
            $url .= $this->proxy_username . ':' . $this->proxy_password . '@';
        }
        
        $url .= $this->proxy_host . ':' . $this->proxy_port;
        
        return $url;
    }
}
