<?php

namespace Igniter\Api\Classes;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ResponseFactory
{
    /**
     * Respond with a created response.
     *
     * @param mixed $content
     * @param string $transformer
     *
     * @return \Illuminate\Http\Response
     */
    public function created($content = null, $transformer = null)
    {
        $response = is_null($transformer)
            ? new Response($content)
            : $this->callTransformer($transformer, 'make', $content)->response();

        return $response->setStatusCode(201);
    }

    /**
     * Respond with an accepted response.
     *
     * @param mixed $content
     * @param string $transformer
     *
     * @return \Illuminate\Http\Response
     */
    public function accepted($content = null, $transformer = null)
    {
        $response = is_null($transformer)
            ? new Response($content)
            : $this->callTransformer($transformer, 'make', $content)->response();

        return $response->setStatusCode(202);
    }

    /**
     * Respond with a no content response.
     *
     * @return \Illuminate\Http\Response
     */
    public function noContent()
    {
        return (new Response(null))->setStatusCode(204);
    }

    /**
     * Respond with a array content response.
     *
     * @param array $item
     *
     * @return \Illuminate\Http\Response
     */
    public function item($item)
    {
        return new Response($item);
    }

    /**
     * Bind a resource to a transformer and start building a response.
     *
     * @param object $resource
     * @param string $transformer
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function resource($resource, $transformer)
    {
        return $this->callTransformer($transformer, 'make', $resource)->response();
    }

    /**
     * Bind a collection to a transformer and start building a response.
     *
     * @param \Illuminate\Support\Collection $collection
     * @param string $transformer
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function collection(Collection $collection, $transformer)
    {
        return $this->callTransformer($transformer, 'collection', $collection)->response();
    }

    /**
     * Bind a paginator to a transformer and start building a response.
     *
     * @param \Illuminate\Contracts\Pagination\Paginator $paginator
     * @param string $transformer
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function paginator(Paginator $paginator, $transformer)
    {
        return $this->callTransformer($transformer, 'collection', $paginator)->response();
    }

    /**
     * Return an error response.
     *
     * @param string $message
     * @param int $statusCode
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     *
     * @return void
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
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     *
     * @return void
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
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     *
     * @return void
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
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     *
     * @return void
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
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     *
     * @return void
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
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     *
     * @return void
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
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     *
     * @return void
     */
    public function errorMethodNotAllowed($message = 'Method Not Allowed')
    {
        $this->error($message, 405);
    }

    /**
     * Call a resource transformer using a specific method and parameters.
     *
     * @param string $transformer
     * @param string $method
     * @param mixed $result
     *
     * @return \Illuminate\Http\Resources\Json\Resource
     */
    protected function callTransformer($transformer, $method, $result = null)
    {
        if (!$transformer)
            throw new HttpException(500, 'Required property [transformer] missing on the REST Controller.');

        if (!class_exists($transformer))
            throw new HttpException(500, sprintf('Invalid transformer class [%s] specified in REST Controller configuration', $transformer));

        return $transformer::$method($result);
    }
}