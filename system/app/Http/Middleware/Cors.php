<?php

namespace App\Http\Middleware;

use Closure;

class Cors
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        $IlluminateResponse = 'Illuminate\Http\Response';

        $allowedOrigins = ['http://localhost:3000', 'http://localhost:3005', 'https://zolpa.admin.quarkinfotech.com'];
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';

        $headers = [];

        if (in_array($origin, $allowedOrigins)) {
            $headers = [
                'Access-Control-Allow-Origin' => $origin,
                'Access-Control-Allow-Methods' => 'POST, GET, OPTIONS, PUT, PATCH, DELETE',
                'Access-Control-Expose-Headers' => 'Content-Length, X-Kuma-Revision, Set-Cookie, Cache-Control, Content-Language, Content-Type, Expires, Last-Modified, Pragma',
                'Access-Control-Allow-Credentials' => 'true',
                'Access-Control-Allow-Headers' => 'Access-Control-Allow-Headers, Origin, Accept, X-Requested-With, Content-Type, Access-Control-Request-Method, Authorization, Access-Control-Request-Headers',
            ];
        }

        $SymfonyResopnse = 'Symfony\Component\HttpFoundation\Response';

        if ($response instanceof $IlluminateResponse) {
            foreach ($headers as $key => $value) {
                $response->header($key, $value);
            }
            return $response;
        }

        if ($response instanceof $SymfonyResopnse) {
            foreach ($headers as $key => $value) {
                $response->headers->set($key, $value);
            }
            return $response;
        }

        return $response;
    }
}
