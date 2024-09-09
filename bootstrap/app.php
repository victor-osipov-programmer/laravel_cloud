<?php

use App\Exceptions\Forbidden;
use App\Exceptions\GeneralError;
use App\Exceptions\NotFound;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        apiPrefix: '',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
    
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->shouldRenderJsonWhen(fn() => true);

        $exceptions->render(function (ValidationException $e) {
            return response([
                "success" => false,
                "message" => $e->errors()
            ]);
        });
        $exceptions->render(function (NotFoundHttpException $e) {
            throw new NotFound();
        });
        $exceptions->render(function (AccessDeniedHttpException $e) {
            throw new Forbidden();
        });
        $exceptions->render(function (GeneralError $e) {
            return response([
                'message' => $e->message
            ], $e->status);
        });
    })->create();
