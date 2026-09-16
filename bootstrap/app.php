<?php

use App\Exceptions\RegraNegocioException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                if ($e instanceof ValidationException) {
                    return response()->json(['message' => 'Os dados informados são inválidos.', 'errors' => $e->errors()], 422);
                }
                if ($e instanceof RegraNegocioException) {
                    return response()->json(['message' => $e->getMessage()], 409);
                }
                if ($e instanceof HttpExceptionInterface) {
                    $status = $e->getStatusCode();

                    return response()->json(['message' => match ($status) {
                        404 => 'Registro ou rota não encontrado.',
                        405 => 'Método não permitido para esta rota.',
                        default => 'Não foi possível processar a solicitação.',
                    }], $status);
                }

                return response()->json(['message' => 'Erro interno. Não foi possível concluir a operação.'], 500);
            }
            if ($e instanceof RegraNegocioException) {
                return redirect()->back()->withInput()->with('error', $e->getMessage());
            }
            if ($e instanceof QueryException) {
                return redirect()->back()->withInput()->with('error', 'Erro interno no banco de dados. Não foi possível concluir a operação.');
            }

            return null;
        });
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
