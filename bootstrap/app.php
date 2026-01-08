<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Illuminate\Foundation\Configuration\Middleware $middleware) {
    $middleware->alias([
        'role' => \App\Http\Middleware\CheckRole::class,
    ]);
})
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            // #region agent log
            $logData = [
                'location' => 'app.php:24',
                'message' => 'Exception handler triggered',
                'data' => [
                    'exception_class' => get_class($e),
                    'exception_message' => $e->getMessage(),
                    'exception_code' => $e->getCode(),
                    'exception_file' => $e->getFile(),
                    'exception_line' => $e->getLine(),
                    'request_uri' => $request->getRequestUri(),
                ],
                'timestamp' => time() * 1000,
                'sessionId' => 'debug-session',
                'runId' => 'run1',
                'hypothesisId' => 'E'
            ];
            file_put_contents(base_path('.cursor/debug.log'), json_encode($logData) . "\n", FILE_APPEND);
            // #endregion
            return null; // Let Laravel handle it normally
        });
    })->create();
    