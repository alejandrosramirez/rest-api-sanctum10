<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Exceptions\UnauthorizedException as SpatieUnauthorizedException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Report or log an exception.
     */
    public function report(Throwable $e): void
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function render($request, Throwable $e): Throwable|Response|ResponseFactory|JsonResponse
    {
        if (!env('APP_ENABLE_CUSTOM_HANDLER')) {
            return parent::render($request, $e);
        }

        if ($e instanceof GenericException) {
            return $this->errorResponse(
                'generic_error',
                $e->getMessage(),
                Response::HTTP_BAD_REQUEST
            );
        }

        if ($e instanceof AuthenticationException) {
            return $this->errorResponse(
                'authentication_error',
                __('Unauthenticated.'),
                Response::HTTP_UNAUTHORIZED
            );
        }

        if (
            $e instanceof UnauthorizedException ||
            $e instanceof AuthorizationException ||
            $e instanceof SpatieUnauthorizedException
        ) {
            return $this->errorResponse(
                'unauthorized_error',
                __('You don’t have permissions to access this resource.'),
                Response::HTTP_FORBIDDEN
            );
        }

        if ($e instanceof ModelNotFoundException) {
            return $this->errorResponse(
                'model_error',
                __(':model doesn’t found.', ['model' => __(class_basename($e->getModel()))]),
                Response::HTTP_NOT_FOUND
            );
        }

        if ($e instanceof NotFoundHttpException || $e instanceof RouteNotFoundException) {
            return $this->errorResponse(
                'not_found_http_error',
                __('URL doesn’t exist.'),
                Response::HTTP_NOT_FOUND
            );
        }

        if ($e instanceof MethodNotAllowedHttpException) {
            return $this->errorResponse(
                'method_not_allowed_error',
                __('The specified method isn’t valid on this request.'),
                Response::HTTP_METHOD_NOT_ALLOWED
            );
        }

        if ($e instanceof NotAcceptableHttpException) {
            return $this->errorResponse(
                'not_acceptable_http_error',
                __('The specified accept header isn’t valid on this request.'),
                Response::HTTP_NOT_ACCEPTABLE
            );
        }

        if ($e instanceof QueryException) {
            return $this->errorResponse(
                'query_error',
                __('A error ocurred processing this data.'),
                Response::HTTP_CONFLICT
            );
        }

        if ($e instanceof PostTooLargeException) {
            return $this->errorResponse(
                'post_too_large_error',
                __('Request body is too large.'),
                Response::HTTP_REQUEST_ENTITY_TOO_LARGE
            );
        }

        if ($e instanceof UnsupportedMediaTypeHttpException) {
            return $this->errorResponse(
                'unsupported_media_type_http_error',
                __('The specified media type isn’t valid on this request.'),
                Response::HTTP_UNSUPPORTED_MEDIA_TYPE
            );
        }

        if ($e instanceof ValidationException) {
            return $this->errorResponse(
                'validation_error',
                $e->validator->errors()->getMessages(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        if ($e instanceof ThrottleRequestsException) {
            return $this->errorResponse(
                'throttle_requests_error',
                __('Too many attempts, please, try again later.'),
                Response::HTTP_TOO_MANY_REQUESTS
            );
        }

        return $this->errorResponse(
            'internal_server_error',
            __('A internal server error has ocurred.'),
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }

    /**
     * Print error response.
     *
     * @param  string  $type
     * @param  string|object|array  $message
     * @param  int  $statusCode
     */
    private function errorResponse(string $error, string|object|array $message, int $statusCode): Response|ResponseFactory|JsonResponse
    {
        return response()->json([
            'error' => $error,
            'message' => $message,
            'status' => $statusCode,
        ], $statusCode);
    }
}
