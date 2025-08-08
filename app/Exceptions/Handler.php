<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Throwable;

class Handler extends ExceptionHandler
{
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
           //
        });

        $this->renderable(function (Throwable $e, $request) {
            return $this->handleException($e, $request);
        });
    }

    public function handleException(Throwable $exception, $request)
    {
        $isApiRequest = $request->is('api/*') || $request->wantsJson();

        if ($exception instanceof ValidationException) {
            return $isApiRequest
                ? errorResponse('Validation Error', 422, $exception->errors())
                : parent::render($request, $exception);
        }

        if ($exception instanceof AuthenticationException) {
            return $isApiRequest
                ? errorResponse('Unauthenticated', 401)
                : redirect()->guest(route('login'));
        }

        if ($exception instanceof AccessDeniedHttpException) {
            return $isApiRequest
                ? errorResponse('Unauthorized', 403)
                : parent::render($request, $exception);
        }

        if ($exception instanceof ModelNotFoundException) {
            return $isApiRequest
                ? errorResponse('Resource not found', 404)
                : parent::render($request, $exception);
        }

        if ($exception instanceof ThrottleRequestsException) {
            return $isApiRequest
                ? errorResponse('Too many attempts', 429)
                : parent::render($request, $exception);
        }

        if ($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();
            $message = $exception->getMessage() ?: 'HTTP error';
            return $isApiRequest
                ? errorResponse($message, $statusCode)
                : parent::render($request, $exception);
        }

        $statusCode = 500;
        $message = config('app.debug') ? $exception->getMessage() : 'Internal server error';

        return $isApiRequest
            ? errorResponse($message, $statusCode)
            : parent::render($request, $exception);
    }
}
