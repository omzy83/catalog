<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Http\Response;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($request->expectsJson()) {
            if ($exception instanceof NotFoundHttpException) {
                $status = Response::HTTP_NOT_FOUND;
                $message = 'Route not found';
            }
            elseif ($exception instanceof ModelNotFoundException) {
                $status = Response::HTTP_NOT_FOUND;
                $message = 'Resource not found';
            }
            elseif ($exception instanceof MethodNotAllowedHttpException) {
                $status = Response::HTTP_METHOD_NOT_ALLOWED;
                $message = 'Method not allowed';
            }
            elseif ($exception instanceof AuthenticationException) {
                $status = Response::HTTP_UNAUTHORIZED;
                $message = 'Authentication required';
            }
            elseif ($exception instanceof ValidationException) {
                return $this->invalidJson($request, $exception);
            }
            else {
                $status = $exception->getStatusCode();
                $message = $exception->getMessage();
            }

            return response()->json(['message' => $message], $status);
        }

        return parent::render($request, $exception);
    }
}
