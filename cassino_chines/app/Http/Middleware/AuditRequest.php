<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;

class AuditRequest
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Loga apenas ações de escrita no painel admin
        try {
            $isAdminPath = str_starts_with($request->path(), trim(env('FILAMENT_BASE_URL', 'admin'), '/') . '/');
        } catch (\Throwable $e) {
            $isAdminPath = false;
        }

        if ($isAdminPath && in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $user = $request->user();
            $payload = $request->except(['password', 'password_confirmation', 'token']);

            AuditLog::create([
                'user_id' => $user?->id,
                'event' => 'action',
                'module' => 'http.request',
                'target_type' => null,
                'target_id' => null,
                'route' => optional($request->route())->getName(),
                'method' => $request->method(),
                'ip' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 500),
                'request' => $payload,
                'message' => 'Ação HTTP no painel',
            ]);
        }

        return $response;
    }
}

