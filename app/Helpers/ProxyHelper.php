<?php

namespace App\Helpers;

use App\Models\ProxySetting;
use Illuminate\Support\Facades\Cache;

class ProxyHelper
{
    /**
     * Get proxy configuration for HTTP client
     *
     * @return array
     */
    public static function getHttpClientConfig()
    {
        return Cache::remember('proxy_settings', 300, function () {
            $proxySetting = ProxySetting::getActive();
            if (!$proxySetting) {
                return [];
            }
            
            $config = $proxySetting->getHttpClientConfig();
            
            // Log para debug
            \Illuminate\Support\Facades\Log::info('ProxyHelper - Configuração gerada', [
                'proxy_enabled' => $proxySetting->proxy_enabled,
                'proxy_host' => $proxySetting->proxy_host,
                'proxy_port' => $proxySetting->proxy_port,
                'proxy_type' => $proxySetting->proxy_type,
                'config_generated' => $config
            ]);
            
            return $config;
        });
    }

    /**
     * Check if proxy is enabled
     *
     * @return bool
     */
    public static function isEnabled()
    {
        return Cache::remember('proxy_enabled', 300, function () {
            return ProxySetting::isEnabled();
        });
    }

    /**
     * Get proxy URL
     *
     * @return string|null
     */
    public static function getProxyUrl()
    {
        return Cache::remember('proxy_url', 300, function () {
            $proxySetting = ProxySetting::getActive();
            return $proxySetting ? $proxySetting->getProxyUrl() : null;
        });
    }

    /**
     * Get all proxy settings
     *
     * @return ProxySetting|null
     */
    public static function getSettings()
    {
        return Cache::remember('proxy_settings_full', 300, function () {
            return ProxySetting::first();
        });
    }

    /**
     * Clear proxy cache
     *
     * @return void
     */
    public static function clearCache()
    {
        Cache::forget('proxy_settings');
        Cache::forget('proxy_enabled');
        Cache::forget('proxy_url');
        Cache::forget('proxy_settings_full');
        
        // Log para debug
        \Illuminate\Support\Facades\Log::info('ProxyHelper - Cache limpo');
    }

    /**
     * Apply proxy to HTTP client
     *
     * @param \Illuminate\Http\Client\PendingRequest $client
     * @return \Illuminate\Http\Client\PendingRequest
     */
    public static function applyToHttpClient($client)
    {
        $config = self::getHttpClientConfig();
        
        if (!empty($config)) {
            if (isset($config['proxy'])) {
                $client->withOptions(['proxy' => $config['proxy']]);
            }
            
            if (isset($config['verify'])) {
                $client->withOptions(['verify' => $config['verify']]);
            }
        }
        
        return $client;
    }

    /**
     * Get proxy configuration for cURL
     *
     * @return array
     */
    public static function getCurlConfig()
    {
        if (!self::isEnabled()) {
            return [];
        }

        $settings = self::getSettings();
        if (!$settings) {
            return [];
        }

        $config = [
            CURLOPT_PROXY => $settings->proxy_host,
            CURLOPT_PROXYPORT => $settings->proxy_port,
            CURLOPT_PROXYTYPE => self::getCurlProxyType($settings->proxy_type),
        ];

        if ($settings->proxy_username && $settings->proxy_password) {
            $config[CURLOPT_PROXYUSERPWD] = $settings->proxy_username . ':' . $settings->proxy_password;
        }

        if (!$settings->proxy_verify_ssl) {
            $config[CURLOPT_SSL_VERIFYPEER] = false;
            $config[CURLOPT_SSL_VERIFYHOST] = false;
        }

        return $config;
    }

    /**
     * Get cURL proxy type constant
     *
     * @param string $type
     * @return int
     */
    private static function getCurlProxyType($type)
    {
        return match ($type) {
            'http' => CURLPROXY_HTTP,
            'https' => CURLPROXY_HTTPS,
            'socks4' => CURLPROXY_SOCKS4,
            'socks5' => CURLPROXY_SOCKS5,
            default => CURLPROXY_HTTP,
        };
    }
}
