<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo(fn() => abort(404));
        $middleware->redirectUsersTo(fn() => route('admin.dashboard'));
        $middleware->web(append: [
            \App\Http\Middleware\WebMiddleware::class,
            \App\Http\Middleware\LocaleMiddleware::class,
        ]);
        $middleware->api(append: [
            \App\Http\Middleware\ApiMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Exception $e, Request $request) {
            if ($request->is('api/*')) {
                if ($e instanceof ModelNotFoundException) {
                    return response()->json(['status' => 0, 'message' => 'Model Not Found'], Response::HTTP_NOT_FOUND);
                }
                if ($e instanceof NotFoundHttpException) {
                    return response()->json(['status' => 0, 'message' => 'Not Found'], Response::HTTP_NOT_FOUND);
                }
                if ($e instanceof MethodNotAllowedHttpException) {
                    return response()->json(['status' => 0, 'message' => 'Method Not Allowed'], Response::HTTP_METHOD_NOT_ALLOWED);
                }
                if ($e instanceof Exception) {
                    return response()->json(['status' => 0, 'message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
                }
            }
        });
    })->create();
