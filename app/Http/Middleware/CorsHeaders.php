<?php

namespace App\Http\Middleware;

use Closure;

class CorsHeaders
{
    public function handle($request, Closure $next)
    {
      $response = $next($request);

      $headers = [
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
        'Access-Control-Allow-Headers' => 'Content-Type, Origin',
      ];

      return $response->withHeaders($headers);
    }
}
