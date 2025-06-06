<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Auth\AuthenticationException;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Throwable $e)
    {

        if ($request->expectsJson() || $request->is('api/*')) {
            if ($e instanceof UnauthorizedException) {
                // $permission = $e->getMessage();
                $message = "You do not have the permission to perform this action.";
                return response()->json([
                    'error' => true,
                    'message' => $message
                ], 403);
            }

            if ($this->isHttpException($e)) {
                $statusCode = $e->getStatusCode();

                switch ($statusCode) {
                    case 401:
                        return response()->json(['message' => 'Please login first'], 401);
                    case 403:
                        return response()->json(['message' => 'Access forbidden'], 403);
                    case 404:
                        return response()->json(['message' => 'Resource not found'], 404);
                    case 419:
                        return response()->json(['message' => 'CSRF token mismatch'], 419);
                    case 429:
                        return response()->json(['message' => 'Too many requests'], 429);
                    case 500:
                        return response()->json(['message' => 'Internal server error'], 500);
                }
            }

            if ($e instanceof \Illuminate\Session\TokenMismatchException) {
                return response()->json(['message' => 'CSRF token mismatch'], 419);
            }

            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], 500);
        }

        if ($this->isHttpException($e)) {
            $statusCode = $e->getStatusCode();

            switch ($statusCode) {
                case 401:
                    return response()->view('errors.401', [], 401);
                case 403:
                    return response()->view('errors.403', [], 403);
                case 419:
                    return response()->view('errors.419', [], 419);
                case 429:
                    return response()->view('errors.429', [], 429);
                case 500:
                    return response()->view('errors.500', [], 500);
            }
        }

        if ($e instanceof \Illuminate\Session\TokenMismatchException) {
            return response()->view('errors.419', [], 419);
        }

        return parent::render($request, $e);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Please login first'], 401);
        }

        return redirect()->guest(route('login'));
    }
}
