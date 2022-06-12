<?php

namespace Igniter\Api\Traits;

use Exception;
use Igniter\Api\Classes\Fractal;

trait CreatesResponse
{
    /**
     * Get the response factory instance.
     *
     * @return \Spatie\Fractal\Fractal
     */
    public function response()
    {
        throw new Exception('Deprecated, use Fractal::create() or response()->json() instead');
    }

    public function fractal()
    {
        return Fractal::create();
    }
}
