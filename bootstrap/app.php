<?php declare(strict_types=1);

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\{Exceptions, Middleware};
use Illuminate\Http\{JsonResponse, Request, Response};
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void
    {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void
    {
        $exceptions->render(function (ModelNotFoundException $e, Request $request)
        {
            if ($request->expectsJson())
            {
                return response()->json([
                    'message' => 'Resource not found.',
                ], Response::HTTP_NOT_FOUND);
            }
        });

        $exceptions->render(function (ValidationException $e, Request $request)
        {
            if ($request->expectsJson())
            {
                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors'  => $e->errors(),
                ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
        });
    })
    ->create();
