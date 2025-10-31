<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response as IlluminateResponse;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class Cors
{
    public function handle(Request $request, Closure $next)
    {
        $allowedOrigins = [
            'http://localhost:3000',
            'http://localhost:3005',
            'https://admin.singingbowlvillagenepal.com',
        ];

        $origin = $request->headers->get('Origin');

        $headers = [];

        if ($origin && in_array($origin, $allowedOrigins, true)) {
            $allowedHeaders = [
                'Access-Control-Allow-Headers',
                'Origin',
                'Accept',
                'X-Requested-With',
                'Content-Type',
                'Access-Control-Request-Method',
                'Authorization',
                'Access-Control-Request-Headers',
                'X-CSRF-TOKEN',
                'X-XSRF-TOKEN',
                'XSRF-TOKEN',
            ];

            $headers = [
                'Access-Control-Allow-Origin' => $origin,
                'Access-Control-Allow-Methods' => 'POST, GET, OPTIONS, PUT, PATCH, DELETE',
                'Access-Control-Expose-Headers' => 'Content-Length, X-Kuma-Revision, Set-Cookie, Cache-Control, Content-Language, Content-Type, Expires, Last-Modified, Pragma',
                'Access-Control-Allow-Credentials' => 'true',
                'Access-Control-Allow-Headers' => implode(', ', $allowedHeaders),
                'Access-Control-Max-Age' => '86400',
            ];
        }

        $response = $request->getMethod() === 'OPTIONS'
            ? response('', 204)
            : $next($request);

        if ($response instanceof IlluminateResponse) {
            foreach ($headers as $key => $value) {
                $response->header($key, $value);
            }

            return $response;
        }

        if ($response instanceof SymfonyResponse) {
            foreach ($headers as $key => $value) {
                $response->headers->set($key, $value);
            }

            return $response;
        }

        return $response;
    }
}
