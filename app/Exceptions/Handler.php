<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Throwable;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    protected $levels = [
        //
    ];

    protected $dontReport = [
        \League\OAuth2\Server\Exception\OAuthServerException::class,
    ];

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register()
    {
        $this->reportable(function (Throwable $e) {
            $this->reportable(function (\League\OAuth2\Server\Exception\OAuthServerException $e) {
                if ($e->getCode() == 9) {
                    return false;
                }
            });
        });
    }

    protected function unauthenticated(
        $request,
        AuthenticationException $exception
    ) {
        if ($request->expectsJson() || $request->is('api/*')) {

            return response()->json([
                'success' => false,
                'statusCode' => 401,
                'message' => 'Unauthenticated. Please provide a valid access token.',
            ], 401);
        }

        return redirect()->guest(
            $exception->redirectTo($request) ?? route('login')
        );
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof NotFoundHttpException) {
            return response()->view('errors.404', [], 404);
        }

        if ($exception instanceof \Illuminate\Contracts\Container\BindingResolutionException) {
            return response()->view('errors.offline', [], 500);
        }

        return parent::render($request, $exception);
    }
}