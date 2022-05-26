<?php

namespace Igniter\Api\Traits;

trait CreatesResponse
{
    /**
     * Get the response factory instance.
     *
     * @return \Spatie\Fractal\Fractal
     */
    public function response()
    {
        return app('api.response');
    }
}
