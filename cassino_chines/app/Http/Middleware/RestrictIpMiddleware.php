<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RestrictIpMiddleware
{
    // Restrinja os IPs permitidos
    protected $allowedIps = [
    ];

    public function handle(Request $request, Closure $next)
    {
        if (!in_array($request->ip(), $this->allowedIps)) {
            // Responde com 'Forbidden' se o IP não está na lista
            return response()->json(['message' => 'IP not allowed'], 403);
        }

        return $next($request);
    }
}

