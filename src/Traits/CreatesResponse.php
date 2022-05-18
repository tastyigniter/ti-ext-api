<?php

namespace Igniter\Api\Traits;

trait CreatesResponse
{
    /**
     * Get the response factory instance.
     *
     * @return \Igniter\Api\Classes\ResponseFactory
     */
    public function response()
    {
        return app('api.response');
    }
}
