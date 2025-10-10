<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Global Middleware
        $middleware->use([
            \App\Http\Middleware\TrustProxies::class,
            \Illuminate\Http\Middleware\HandleCors::class,
            \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
            \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
            \App\Http\Middleware\TrimStrings::class,
            \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
            \App\Http\Middleware\ForceJsonResponse::class,
            \App\Http\Middleware\Cors::class
        ]);

        // Middleware Groups
        $middleware->group('web', [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        $middleware->group('api', [
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        $middleware->group('admin', [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        $middleware->group('user', [
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\UpdateLastActive::class,
        ]);

        // Middleware Aliases
        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
            'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
            'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
            'can' => \Illuminate\Auth\Middleware\Authorize::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
            'precognitive' => \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
            'signed' => \App\Http\Middleware\ValidateSignature::class,
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            'abilities' => \Laravel\Sanctum\Http\Middleware\CheckAbilities::class,
            'json.response' => \App\Http\Middleware\ForceJsonResponse::class,
            'cors' => \App\Http\Middleware\Cors::class
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('queue:work --stop-when-empty')
            ->everyFiveMinutes()
            ->withoutOverlapping()
            ->before(function () {
                Log::info('Starting the queue:work command.');
            })
            ->after(function () {
                Log::info('Completed the queue:work command.');
            });

        $schedule->command('orders:check-status')
            ->hourly()
            ->between('8:00', '21:00')
            ->withoutOverlapping()
            ->before(function () {
                Log::info('Starting the orders:check-status command.');
            })
            ->after(function () {
                Log::info('Completed the orders:check-status command.');
            });

        // Deactivate expired limited time deals - every hour
        $schedule->command('deals:deactivate-expired')
            ->hourly()
            ->withoutOverlapping()
            ->before(function () {
                Log::info('Starting deactivation of expired limited time deals.');
            })
            ->after(function () {
                Log::info('Completed deactivation of expired limited time deals.');
            });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (\Exception|ValidationException $e, $request) {
            $code = $e->getCode();
            $statusCode = (is_int($code) && $code >= 100 && $code < 600) ? $code : 400;
            return response()->json([
                'error' => $e->getMessage(),
                'status' => $statusCode,
                'errors' => method_exists($e, 'errors') ? $e->errors() : $e->getTrace(),
            ], $statusCode);
        });
    })
    ->withCommands([
        __DIR__.'/../app/Console/Commands',
        __DIR__.'/../Modules/*/App/Console',
    ])
    ->create();