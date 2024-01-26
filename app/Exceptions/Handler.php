<?php

namespace App\Exceptions;

use App\Http\Resources\UserResource;
use App\Traits\ResponseTrait;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ResponseTrait;

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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
    /**
     * @param $request
     * @param Throwable $e
     *
     * @return JsonResponse
     *
     * @throws Throwable
     */
    public function render($request, Throwable $e): JsonResponse
    {
        if ($e instanceof ValidationException) {
            $errors = $e->errors();
            return $this->responseValidationFailed($e->getMessage(), $errors);
        }

        if (
            $e instanceof AppValidationException ||
            $e->getCode() === Response::HTTP_UNPROCESSABLE_ENTITY
        ) {
            return $this->responseValidationFailed($e->getMessage());
        }

        if (
            $e instanceof NotFoundException ||
            $e instanceof ModelNotFoundException ||
            $e instanceof NotFoundHttpException ||
            $e->getCode() === Response::HTTP_NOT_FOUND
        ) {
            return $this->responseNotFound($e->getMessage());
        }

        if (
            $e instanceof AuthenticationException  ||
            $e->getCode() === Response::HTTP_UNAUTHORIZED
        ) {
            return $this->responseUnauthenticated();
        }

        if (
            $e instanceof UnauthorizedException ||
            $e instanceof AuthorizationException ||
            $e->getCode() === Response::HTTP_FORBIDDEN
        ) {
            return $this->responseUnauthorized();
        }

        if (
            $e instanceof ConnectionException ||
            $e->getCode() === Response::HTTP_REQUEST_TIMEOUT
        ) {
            return $this->responseRequestTimeout();
        }

        if (
            $e instanceof DuplicateException ||
            $e->getCode() === Response::HTTP_CONFLICT
        ) {
            return $this->responseCustom(Response::HTTP_CONFLICT, $e->getMessage());
        }

        if (
            $e instanceof TooManyRequestsHttpException ||
            $e->getCode() === Response::HTTP_TOO_MANY_REQUESTS
        ) {
            return $this->responseCustom(Response::HTTP_TOO_MANY_REQUESTS, $e->getMessage());
        }

        if (
            $e instanceof BadRequestException ||
            $e->getCode() === Response::HTTP_BAD_REQUEST
        ) {
            return $this->responseBadRequest($e->getMessage());
        }

        if (!in_array(config('app.env'), ['local', 'dev', 'development'])) {
            return $this->responseBadRequest();
        }

        $traces = array_filter($e->getTrace(), fn ($trace) => isset($trace['file']) && strpos($trace['file'], '/app/'));

        $requestPayload = [
            'auth_user' => UserResource::make(auth()->user()),
            'info' => [
                'method' => $request->method(),
                'name' => $request->route()->getName() ?? '',
                'route' => $request->route()->uri() ?? '',
                'controller' => $request->route()->getAction('controller') ?? '',
            ],
            'data' => [
                'query' => $request->query->all(),
                'parameters' => $request->route()->parameters() ?? '',
                'body' => $request->request->all(),
            ],
        ];

        return $this->responseBadRequest($e->getMessage(), null, $traces, $requestPayload);
    }
}
