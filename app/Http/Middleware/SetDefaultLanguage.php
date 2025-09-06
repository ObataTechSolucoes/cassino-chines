<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


class SetDefaultLanguage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        // Verificar se é uma requisição da API e se o usuário está autenticado
        if (auth('api')->check()) {
            try {
                $user = auth('api')->user();
                if ($user && isset($user->language)) {
                    app()->setLocale($user->language);
                }
            } catch (\Exception $e) {
                // Log do erro mas não interrompe o fluxo
                \Log::warning('Erro ao definir idioma: ' . $e->getMessage());
            }
        }

        return $next($request);
    }
}
