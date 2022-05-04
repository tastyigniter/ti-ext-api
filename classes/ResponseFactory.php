<?php

namespace Igniter\Api\Classes;

use Closure;
use Dingo\Api\Http\Response;
use Dingo\Api\Http\Response\Factory;
use ErrorException;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ResponseFactory
{
    /**
     * @var \Dingo\Api\Http\Response\Factory
     */
    protected $factory;

    /**
     * @var \Dingo\Api\Transformer\Factory
     */
    protected $transformer;

    public function __construct(Factory $factory, $transformer)
    {
        $this->factory = $factory;
        $this->transformer = $transformer;
    }

    /**
     * Respond with a created response.
     *
     * @param mixed $content
     * @param mixed $transformer
     *
     * @param null $location
     * @param array $parameters
     * @param \Closure|null $after
     * @return \Illuminate\Http\Response
     */
    public function created($content = null, $transformer = null, $location = null, $parameters = [], Closure $after = null)
    {
        $binding = $this->registerTransformer($content, $transformer, $parameters, $after);

        $response = new Response($content, 201, [], $binding);

        if (!is_null($location))
            $response->header('Location', $location);

        return $response->setStatusCode(201);
    }

    /**
     * Respond with an accepted response.
     *
     * @param mixed $content
     * @param mixed $transformer
     *
     * @param null $location
     * @param array $parameters
     * @param \Closure|null $after
     * @return \Illuminate\Http\Response
     */
    public function accepted($content = null, $transformer = null, $location = null, $parameters = [], Closure $after = null)
    {
        $binding = $this->registerTransformer($content, $transformer, $parameters, $after);

        $response = new Response($content, 202, [], $binding);

        if (!is_null($location))
            $response->header('Location', $location);

        return $response->setStatusCode(202);
    }

    /**
     * Respond with a no content response.
     *
     * @return \Illuminate\Http\Response
     */
    public function noContent()
    {
        return $this->factory->noContent();
    }

    public function array($array, $transformer = null, array $parameters = [], Closure $after = null)
    {
        return $this->factory->array($array, $transformer, $parameters, $after);
    }

    /**
     * Respond with a array content response.
     *
     * @param mixed $item
     * @param mixed $transformer
     * @param array $parameters
     * @param \Closure|null $after
     *
     * @return \Illuminate\Http\Response
     */
    public function item($item, $transformer, $parameters = [], Closure $after = null)
    {
        return $this->factory->item($item, $transformer, $parameters, $after);
    }

    /**
     * Bind a resource to a transformer and start building a response.
     *
     * @param object $resource
     * @param mixed $transformer
     *
     * @param array $parameters
     * @param \Closure|null $after
     * @return \Illuminate\Http\Response
     */
    public function resource($resource, $transformer, $parameters = [], Closure $after = null)
    {
        return $this->item($resource, $transformer, $parameters, $after);
    }

    /**
     * Bind a collection to a transformer and start building a response.
     *
     * @param \Illuminate\Support\Collection $collection
     * @param mixed $transformer
     *
     * @param array $parameters
     * @param \Closure|null $after
     * @return \Illuminate\Http\Response
     */
    public function collection(Collection $collection, $transformer = null, $parameters = [], Closure $after = null)
    {
        return $this->factory->collection($collection, $transformer, $parameters, $after);
    }

    /**
     * Bind a paginator to a transformer and start building a response.
     *
     * @param \Illuminate\Contracts\Pagination\Paginator $paginator
     * @param mixed $transformer
     *
     * @param array $parameters
     * @param \Closure|null $after
     * @return \Illuminate\Http\Response
     */
    public function paginator(Paginator $paginator, $transformer = null, $parameters = [], Closure $after = null)
    {
        return $this->factory->paginator($paginator, $transformer, $parameters, $after);
    }

    /**
     * Return an error response.
     *
     * @param string $message
     * @param int $statusCode
     *
     * @return void
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function error($message, int $statusCode)
    {
        throw new HttpException($statusCode, $message);
    }

    /**
     * Return a 404 not found error.
     *
     * @param string $message
     *
     * @return void
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function errorNotFound($message = 'Not Found')
    {
        $this->error($message, 404);
    }

    /**
     * Return a 400 bad request error.
     *
     * @param string $message
     *
     * @return void
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function errorBadRequest($message = 'Bad Request')
    {
        $this->error($message, 400);
    }

    /**
     * Return a 403 forbidden error.
     *
     * @param string $message
     *
     * @return void
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function errorForbidden($message = 'Forbidden')
    {
        $this->error($message, 403);
    }

    /**
     * Return a 500 internal server error.
     *
     * @param string $message
     *
     * @return void
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function errorInternal($message = 'Internal Error')
    {
        $this->error($message, 500);
    }

    /**
     * Return a 401 unauthorized error.
     *
     * @param string $message
     *
     * @return void
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function errorUnauthorized($message = 'Unauthorized')
    {
        $this->error($message, 401);
    }

    /**
     * Return a 405 method not allowed error.
     *
     * @param string $message
     *
     * @return void
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function errorMethodNotAllowed($message = 'Method Not Allowed')
    {
        $this->error($message, 405);
    }

    public function __call($method, $parameters)
    {
        if (method_exists($this->factory, $method))
            return call_user_func_array([$this->factory, $method], $parameters);

        throw new ErrorException('Undefined method '.get_class($this).'::'.$method);
    }

    protected function registerTransformer($resource, $transformer = null, array $parameters = [], Closure $after = null)
    {
        if (is_null($transformer))
            return null;

        $class = is_object($resource)
            ? get_class($resource)
            : \stdClass::class;

        return $this->transformer->register($class, $transformer, $parameters, $after);
    }
}
