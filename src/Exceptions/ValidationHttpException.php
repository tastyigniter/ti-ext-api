<?php

namespace Igniter\Api\Exceptions;

use Throwable;

class ValidationHttpException extends ResourceException
{
    /**
     * Create a new validation HTTP exception instance.
     *
     * @param \Illuminate\Support\MessageBag|array $errors
     * @param array $headers
     * @param int $code
     *
     * @return void
     */
    public function __construct($errors = null, ?Throwable $previous = null, $headers = [], $code = 0)
    {
        parent::__construct('', $errors, $previous, $headers, $code);
    }
}
