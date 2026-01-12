<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Log all exceptions with full details
        $exceptions->respond(function (\Symfony\Component\HttpFoundation\Response $response, \Throwable $e) {
            if (config('app.debug')) {
                return response()->json([
                    'error' => $e->getMessage(),
                    'exception' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => array_slice($e->getTrace(), 0, 10),
                ], 500);
            }
            return $response;
        });
    })->create();
