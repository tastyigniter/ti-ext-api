<?php

namespace Igniter\Api\Exceptions;

use Illuminate\Support\MessageBag;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class ResourceException extends HttpException
{
    /**
     * MessageBag errors.
     *
     * @var \Illuminate\Support\MessageBag
     */
    protected $errors;

    /**
     * Create a new resource exception instance.
     *
     * @param string $message
     * @param \Illuminate\Support\MessageBag|array $errors
     * @param \Throwable $previous
     * @param array $headers
     * @param int $code
     *
     * @return void
     */
    public function __construct($message = null, $errors = '', Throwable $previous = null, $headers = [], $code = 0)
    {
        if (is_null($errors)) {
            $this->errors = new MessageBag;
        }
        else {
            $this->errors = is_array($errors) ? new MessageBag($errors) : $errors;
        }

        parent::__construct(422, $message, $previous, $headers, $code);
    }

    /**
     * Get the errors message bag.
     *
     * @return \Illuminate\Support\MessageBag
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Determine if message bag has any errors.
     *
     * @return bool
     */
    public function hasErrors()
    {
        return !$this->errors->isEmpty();
    }
}
