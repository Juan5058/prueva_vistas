<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireMongoDb
{
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->runningUnitTests()) {
            return $next($request);
        }

        if (! extension_loaded('mongodb') || config('database.default') !== 'mongodb') {
            $message = extension_loaded('mongodb')
                ? 'DB_CONNECTION debe ser mongodb. Valor actual: '.config('database.default')
                : 'La extensión PHP mongodb no está cargada. Use Docker: docker compose up --build';

            return response()->view('errors.mongo', compact('message'), 503);
        }

        return $next($request);
    }
}
