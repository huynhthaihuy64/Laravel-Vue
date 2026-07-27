<?php

namespace App\Exceptions;

use Throwable;

class DuplicateException extends \Exception
{
    /**
     * @var string
     */
    protected $message = '%s %s';
    /**
     * @param string $model
     * @param int|string $id
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(
        string $model,
        int|string $id = null,
        int $code = \Symfony\Component\HttpFoundation\Response::HTTP_CONFLICT,
        Throwable $previous = null
    ) {
        parent::__construct(sprintf($this->message, $model, $id), $code, $previous);
    }
}
