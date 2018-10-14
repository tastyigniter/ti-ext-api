<?php

namespace Igniter\Api\Traits;

use Illuminate\Contracts\Support\Arrayable;

trait ResponseMaker
{
    public function fractal($response)
    {
        return fractal($response);
    }

    public function makeResponse($statusCode, $response, $relations = [])
    {
        return $this->fractal($response)
                    ->transformWith($this->makeTransformer())
                    ->parseIncludes($relations)->respond($statusCode);
    }

    /**
     * @param $transformer
     * @return mixed
     */
    protected function makeTransformer()
    {
        if (strlen($this->transformer))
            return new $this->transformer;

        return function ($data) {
            return $this->responseToArray($data);
        };
    }

    protected function responseToArray($data)
    {
        if ($data instanceof Arrayable)
            return $data->toArray();

        return $data;
    }
}