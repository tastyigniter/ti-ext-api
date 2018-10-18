<?php

namespace Igniter\Api\Exception;

use Config;
use Exception;
use Igniter\Flame\Exception\ValidationException as BaseValidationException;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionHandler
{
    protected $format;

    public function __construct($format)
    {
        $this->format = $format;
    }

    public function handleException($exception)
    {
        return $this->getDetailedResponse($exception);
    }

    /**
     * Returns a more descriptive error message if application
     * debug mode is turned on.
     * @param \Exception $exception
     * @return string
     */
    public function getDetailedResponse($exception)
    {
        $format = $this->newResponseArray();

        $statusCode = $this->getStatusCode($exception);

        if (!$message = $exception->getMessage()) {
            $message = sprintf('%d %s', $statusCode, Response::$statusTexts[$statusCode]);
        }

        $response = [];
        $response['status_code'] = $statusCode;
        $response['message'] = $message;

        if ($exception instanceof BaseValidationException) {
            $response['errors'] = $exception->getErrors();
        }

        if ($exception instanceof ValidationException) {
            $response['errors'] = $exception->errors();
            $response['status_code'] = $exception->status;
        }

        if ($code = $exception->getCode()) {
            $response['code'] = $code;
        }

        if (Config::get('app.debug', FALSE)) {
            $response['debug'] = [
                'line' => $exception->getLine(),
                'file' => $exception->getFile(),
                'class' => get_class($exception),
                'trace' => explode("\n", $exception->getTraceAsString()),
            ];
        }

        return array_intersect_key(array_filter($response), array_flip($format));
    }

    /**
     * Create a new response array with replacement values.
     *
     * @return array
     */
    protected function newResponseArray()
    {
        return $this->format;
    }

    /**
     * Get the exception status code.
     *
     * @param \Exception $exception
     * @param int $defaultStatusCode
     *
     * @return int
     */
    protected function getExceptionStatusCode(Exception $exception, $defaultStatusCode = 500)
    {
        return ($exception instanceof HttpExceptionInterface) ? $exception->getStatusCode() : $defaultStatusCode;
    }

    /**
     * Get the status code from the exception.
     *
     * @param \Exception $exception
     *
     * @return int
     */
    protected function getStatusCode(Exception $exception)
    {
        if ($exception instanceof ValidationException) {
            return $exception->status;
        }

        return $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : 500;
    }

    /**
     * Get the headers from the exception.
     *
     * @param \Exception $exception
     *
     * @return array
     */
    protected function getHeaders(Exception $exception)
    {
        return $exception instanceof HttpExceptionInterface ? $exception->getHeaders() : [];
    }
}