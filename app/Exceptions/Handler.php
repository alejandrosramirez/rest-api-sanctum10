<?php

namespace App\Exceptions;

use App\Responses\ErrorResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
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
    public function render($request, Throwable $e): Response|RedirectResponse|ErrorResponse
    {
        if (!env('APP_ENABLE_CUSTOM_HANDLER')) {
            return parent::render($request, $e);
        }

        return match (true) {
            $e instanceof GenericException => new ErrorResponse('generic_error', $e->getMessage(), null, Response::HTTP_BAD_REQUEST),
            $e instanceof AuthenticationException => $this->unauthenticated($request, $e),
            $e instanceof UnauthorizedException, $e instanceof AuthorizationException, $e instanceof SpatieUnauthorizedException => new ErrorResponse('unauthorized_error', __('You don’t have permissions to access this resource.'), null, Response::HTTP_FORBIDDEN),
            $e instanceof ModelNotFoundException => new ErrorResponse('model_error', __(':model doesn’t found.', ['model' => __(class_basename($e->getModel()))]), null, Response::HTTP_NOT_FOUND),
            $e instanceof NotFoundHttpException, $e instanceof RouteNotFoundException => new ErrorResponse('not_found_http_error', __('URL doesn’t exist.'), null, Response::HTTP_NOT_FOUND),
            $e instanceof MethodNotAllowedHttpException => new ErrorResponse( 'method_not_allowed_error', __('The specified method isn’t valid on this request.'), null, Response::HTTP_METHOD_NOT_ALLOWED),
            $e instanceof NotAcceptableHttpException => new ErrorResponse('not_acceptable_http_error', __('The specified accept header isn’t valid on this request.'), null, Response::HTTP_NOT_ACCEPTABLE),
            $e instanceof QueryException => new ErrorResponse('query_error', __('A error ocurred processing this data.'), null, Response::HTTP_CONFLICT),
            $e instanceof PostTooLargeException => new ErrorResponse('post_too_large_error', __('Request body is too large.'), null, Response::HTTP_REQUEST_ENTITY_TOO_LARGE),
            $e instanceof UnsupportedMediaTypeHttpException => new ErrorResponse('unsupported_media_type_http_error', __('The specified media type isn’t valid on this request.'), null, Response::HTTP_UNSUPPORTED_MEDIA_TYPE),
            $e instanceof ValidationException => new ErrorResponse('validation_error', $e->validator->errors()->getMessages(), null, Response::HTTP_UNPROCESSABLE_ENTITY),
            $e instanceof ThrottleRequestsException => new ErrorResponse('throttle_requests_error', __('Too many attempts, please, try again later.'), null, Response::HTTP_TOO_MANY_REQUESTS),
            default => new ErrorResponse('internal_server_error', __('A internal server error has ocurred.'), $e, Response::HTTP_INTERNAL_SERVER_ERROR),
        };
    }

    /**
     * Convert an authentication exception into a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $this->shouldReturnJson($request, $exception)
            ? new ErrorResponse('authentication_error', __('Unauthenticated.'), null, Response::HTTP_UNAUTHORIZED)
            : redirect()->guest(route('welcome'));
    }
}
