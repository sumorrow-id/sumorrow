<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\RedirectIfAdmin;
use App\Http\Middleware\SetLocale;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        apiPrefix: 'api',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Azure App Service terminates TLS at its front end; without this the
        // app sees plain HTTP and renders http:// asset URLs (mixed content).
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'admin' => AdminMiddleware::class,
            'redirect.admin' => RedirectIfAdmin::class,
        ]);
        $middleware->web(append: [
            SetLocale::class,
        ]);
        $middleware->redirectTo(
            guests: '/login',
            users: '/home'
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $wantsJson = fn (Request $request) => $request->is('api/*') || $request->expectsJson();

        $exceptions->render(function (AuthenticationException $e, Request $request) use ($wantsJson) {
            if ($wantsJson($request)) {
                return response()->json([
                    'message' => 'Unauthenticated.',
                    'status' => 401,
                ], 401);
            }
        });

        $exceptions->render(function (ValidationException $e, Request $request) use ($wantsJson) {
            if ($wantsJson($request)) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'status' => 422,
                    'errors' => $e->errors(),
                ], 422);
            }
        });

        $exceptions->render(function (ModelNotFoundException $e, Request $request) use ($wantsJson) {
            if ($wantsJson($request)) {
                return response()->json([
                    'message' => 'Resource not found.',
                    'status' => 404,
                ], 404);
            }
        });

        $exceptions->render(function (NotFoundHttpException $e, Request $request) use ($wantsJson) {
            if ($wantsJson($request)) {
                return response()->json([
                    'message' => 'Endpoint not found.',
                    'status' => 404,
                ], 404);
            }
        });

        $exceptions->render(function (ThrottleRequestsException $e, Request $request) use ($wantsJson) {
            if ($wantsJson($request)) {
                return response()->json([
                    'message' => 'Too many requests. Slow down.',
                    'status' => 429,
                    'retry_after' => (int) ($e->getHeaders()['Retry-After'] ?? 60),
                ], 429, $e->getHeaders());
            }
        });
    })->create();
