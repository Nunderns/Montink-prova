<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class VerifyWebhookIp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Se a restrição por IP estiver desativada, permite a requisição
        if (!config('services.webhook.enable_ip_restriction', false)) {
            return $next($request);
        }

        $allowedIps = config('services.webhook.allowed_ips');
        $clientIp = $request->ip();

        // Se não houver IPs permitidos configurados, bloqueia por padrão
        if (empty($allowedIps)) {
            Log::warning('Acesso ao webhook bloqueado: Nenhum IP permitido configurado', [
                'client_ip' => $clientIp,
                'path' => $request->path()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Acesso não autorizado.'
            ], Response::HTTP_FORBIDDEN);
        }

        // Verifica se o IP do cliente está na lista de permitidos
        if (!in_array($clientIp, $allowedIps)) {
            Log::warning('Tentativa de acesso não autorizado ao webhook', [
                'client_ip' => $clientIp,
                'allowed_ips' => $allowedIps,
                'path' => $request->path()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Acesso não autorizado.'
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
