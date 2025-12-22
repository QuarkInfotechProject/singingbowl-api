<?php

namespace Modules\Cart\App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;
use Symfony\Component\HttpFoundation\Response;

class CartAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Exclude payment callback routes from auth checks
        Log::info('CartAuthMiddleware Path Check', ['path' => $request->path(), 'url' => $request->url()]);
        if ($request->is('api/user/orders/success/*') || $request->is('api/user/orders/payment-fail/*')) {
             return $next($request);
        }

        try {
            // Check for authenticated user
            if (Auth::guard('user')->check()) {
                $request->attributes->add([
                    'cart_type' => 'user',
                    'cart_identifier' => Auth::guard('user')->id()
                ]);

                return $next($request);
            }
        } catch (\Exception $e) {
            // Continue to check for guest token
        }

        // Check for guest token
        $guestToken = $request->header('X-Guest-Token');
        if ($guestToken) {
            $request->attributes->add([
                'cart_type' => 'guest',
                'cart_identifier' => $guestToken
            ]);

            return $next($request);
        }

        // If neither authentication type is valid, return 401
        throw new Exception(
            'Unauthorized. Please provide either a valid authentication token or an X-Guest-Token header.',
            ErrorCode::UNAUTHORIZED
        );
    }
}