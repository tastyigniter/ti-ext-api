<?php

namespace Igniter\Api\Exceptions;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Debug\ExceptionHandler as IlluminateExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class ErrorHandler
{
    /**
     * Generic response format.
     *
     * @var array
     */
    protected $format;

    /**
     * Indicates if we are in debug mode.
     *
     * @var bool
     */
    protected $debug = false;

    /**
     * The parent Illuminate exception handler instance.
     *
     * @var IlluminateExceptionHandler
     */
    protected $parentHandler;

    /**
     * User defined replacements to merge with defaults.
     *
     * @var array
     */
    protected $replacements = [];

    public function __construct(ExceptionHandler $handler, array $format, $debug)
    {
        $this->parentHandler = $handler;
        $this->format = $format;
        $this->debug = $debug;

        $handler->renderable(function (Throwable $ex) {
            return $this->render(request(), $ex);
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Throwable $e
     * @return \Illuminate\Http\Response|void
     */
    public function render($request, Throwable $e)
    {
        // Convert Eloquent's 500 ModelNotFoundException into a 404 NotFoundHttpException
        if ($e instanceof ModelNotFoundException)
            $e = new NotFoundHttpException($e->getMessage(), $e);

        if ($e instanceof ValidationException)
            $e = new ValidationHttpException($e->getErrors(), $e);

        if ($e instanceof \Igniter\Flame\Exception\ValidationException)
            $e = new ValidationHttpException($e->getErrors(), $e);

        if (!$request->routeIs('igniter.api.*'))
            return;

        return $this->genericResponse($e);
    }

    /**
     * Handle a generic error response if there is no handler available.
     *
     * @param Throwable $exception
     *
     * @return \Illuminate\Http\Response
     * @throws Throwable
     *
     */
    protected function genericResponse(Throwable $exception)
    {
        $replacements = $this->prepareReplacements($exception);

        $response = $this->format;

        array_walk_recursive($response, function (&$value, $key) use ($replacements) {
            if (Str::startsWith($value, ':') && isset($replacements[$value])) {
                $value = $replacements[$value];
            }
        });

        $response = $this->recursivelyRemoveEmptyReplacements($response);

        return new Response($response, $this->getStatusCode($exception), $this->getHeaders($exception));
    }

    /**
     * Get the status code from the exception.
     *
     * @param Throwable $exception
     *
     * @return int
     */
    protected function getStatusCode(Throwable $exception)
    {
        if ($exception instanceof ValidationException) {
            $statusCode = $exception->status;
        }
        elseif ($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();
        }
        else {
            // By default throw 500
            $statusCode = 500;
        }

        // Be extra defensive
        if ($statusCode < 100 || $statusCode > 599) {
            $statusCode = 500;
        }

        return $statusCode;
    }

    /**
     * Get the headers from the exception.
     *
     * @param Throwable $exception
     *
     * @return array
     */
    protected function getHeaders(Throwable $exception)
    {
        return $exception instanceof HttpExceptionInterface ? $exception->getHeaders() : [];
    }

    /**
     * Prepare the replacements array by gathering the keys and values.
     *
     * @param Throwable $exception
     *
     * @return array
     */
    protected function prepareReplacements(Throwable $exception)
    {
        $statusCode = $this->getStatusCode($exception);

        if (!$message = $exception->getMessage()) {
            $message = sprintf('%d %s', $statusCode, Response::$statusTexts[$statusCode]);
        }

        $replacements = [
            ':message' => $message,
            ':status_code' => $statusCode,
        ];

        if ($exception instanceof ResourceException && $exception->hasErrors()) {
            $replacements[':errors'] = $exception->getErrors();
        }

        if ($exception instanceof ValidationException) {
            $replacements[':errors'] = $exception->errors();
            $replacements[':status_code'] = $exception->status;
        }

        if ($code = $exception->getCode()) {
            $replacements[':code'] = $code;
        }

        if ($this->debug) {
            $replacements[':debug'] = [
                'line' => $exception->getLine(),
                'file' => $exception->getFile(),
                'class' => get_class($exception),
                'trace' => explode("\n", $exception->getTraceAsString()),
            ];

            // Attach trace of previous exception, if exists
            if (!is_null($exception->getPrevious())) {
                $currentTrace = $replacements[':debug']['trace'];

                $replacements[':debug']['trace'] = [
                    'previous' => explode("\n", $exception->getPrevious()->getTraceAsString()),
                    'current' => $currentTrace,
                ];
            }
        }

        return array_merge($replacements, $this->replacements);
    }

    /**
     * Recursively remove any empty replacement values in the response array.
     *
     * @param array $input
     *
     * @return array
     */
    protected function recursivelyRemoveEmptyReplacements(array $input)
    {
        foreach ($input as &$value) {
            if (is_array($value)) {
                $value = $this->recursivelyRemoveEmptyReplacements($value);
            }
        }

        return array_filter($input, function ($value) {
            if (is_string($value)) {
                return !Str::startsWith($value, ':');
            }

            return true;
        });
    }
}
