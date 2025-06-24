<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
       public function handle($request, Closure $next)
{
    $allowedOrigins = ["*"];

    $origin = $request->headers->get('Origin');

    if ($request->getMethod() === "OPTIONS") {
        $response = response('', 200);
    } else {
        $response = $next($request);
    }

    if (in_array($origin, $allowedOrigins)) {
        $response->headers->set('Access-Control-Allow-Origin', $origin);
        $response->headers->set('Access-Control-Allow-Credentials', 'true');
    }

    $response->headers->set('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE');
    $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With, Accept, Authorization');

    return $response;
}
}

