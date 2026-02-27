<?php

use App\Http\Middleware\CorsMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Appliquer CORS à toutes les routes API
        $middleware->api(prepend: [
            CorsMiddleware::class,
        ]);

        // Rediriger les visiteurs non-authentifiés vers le login du panel utilisateur
        $middleware->redirectGuestsTo('/dashboard/login');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
