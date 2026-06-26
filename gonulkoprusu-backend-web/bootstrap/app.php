<?php

use App\Http\Middleware\AdminMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

$host = $_SERVER['HTTP_HOST'] ?? '';
$isAdminHost = str_starts_with($host, 'admin.') || str_starts_with($host, 'adminlogin.');

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders()
    ->withRouting(
        web: $isAdminHost
            ? __DIR__.'/../routes/adminlogin.php'
            : __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => AdminMiddleware::class,
        ]);

        $middleware->redirectGuestsTo(function (Request $request) {
            $host = $request->getHost();
            if (str_starts_with($host, 'admin.') || str_starts_with($host, 'adminlogin.')) {
                return route('admin.login');
            }

            return route('login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
