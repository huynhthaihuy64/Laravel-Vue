<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AppException extends Exception
{
    /**
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(
        string $message = '',
        int $code = Response::HTTP_BAD_REQUEST,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
