<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e): Response
    {
        // Force JSON response for API requests or when Accept header is application/json
        if ($request->expectsJson() || $request->is('api/*') || $request->header('Accept') === 'application/json') {
            return $this->renderJsonException($request, $e);
        }

        return parent::render($request, $e);
    }

    /**
     * Render exception as JSON response
     */
    protected function renderJsonException(Request $request, Throwable $e): JsonResponse
    {
        $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;

        // Handle specific exception types
        if ($e instanceof QueryException) {
            $statusCode = 500;
            $message = app()->environment('production')
                ? 'Database error occurred'
                : $e->getMessage();
        } else {
            $message = $e->getMessage() ?: 'An error occurred';
        }

        // Ensure status code is valid
        if ($statusCode < 100 || $statusCode >= 600) {
            $statusCode = 500;
        }

        $response = [
            'code' => -1,
            'message' => $message,
        ];

        // Add debug information in non-production environments
        if (!app()->environment('production')) {
            $response['debug'] = [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ];
        }

        return response()->json($response, $statusCode);
    }
}
