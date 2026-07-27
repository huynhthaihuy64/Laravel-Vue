<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class NotFoundException extends Exception
{
    /**
     * @var string
     */
    protected $message = '%s %s not found';
    /**
     * @param string $model
     * @param int|string $id
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(
        string $model,
        int|string $id = null,
        int $code = Response::HTTP_NOT_FOUND,
        Throwable $previous = null
    ) {
        parent::__construct(sprintf($this->message, substr($model, strrpos($model, '\\') + 1), $id), $code, $previous);
    }
}
