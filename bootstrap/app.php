<?php

use App\Http\Middleware\EnsurePermission;
use App\Http\Middleware\RequireMongoDb;
use App\Http\Middleware\VerifyUserSessionAndIp;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->appendToGroup('web', RequireMongoDb::class);
        $middleware->alias([
            'trd.session' => VerifyUserSessionAndIp::class,
            'permission' => EnsurePermission::class,
        ]);
        $middleware->redirectGuestsTo(fn () => route('login'));
        $middleware->redirectUsersTo(fn () => route('dashboard'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        if (interface_exists(\MongoDB\Driver\Exception\Exception::class)) {
            $exceptions->render(function (\MongoDB\Driver\Exception\Exception $e, Request $request) {
                report($e);

                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'No fue posible conectar con MongoDB 7.',
                    ], 503);
                }

                return response()->view('errors.mongo', [
                    'message' => $e->getMessage(),
                ], 503);
            });
        }
    })->create();
