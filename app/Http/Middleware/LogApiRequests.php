<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogApiRequests
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        Log::channel('daily')->info('API Request/Response', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'request' => $request->all(),
            'response' => $response->getContent(),
            'status' => $response->status()
        ]);

        return $response;
    }
} 