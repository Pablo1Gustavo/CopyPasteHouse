<?php
declare(strict_types=1);
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SendLog
{
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        if (in_array($request->method(), ['OPTIONS', 'HEAD', 'CONNECT']))
        {
            return;
        }
        Log::info("{request_method}:{request_path} - Requisição realizada ({response_status})", [
            'request_path'    => $request->path(),
            'request_method'  => $request->method(),
            "request_query"   => $request->query(),
            'response_status' => $response->getStatusCode(),
        ]);
    }
}
