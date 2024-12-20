<?php

namespace Igniter\Api\Traits;

use Spatie\Fractal\Fractal;

trait CreatesResponse
{
    /**
     * Get the response factory instance.
     *
     * @return \Spatie\Fractal\Fractal
     */
    public function response()
    {
        throw new \LogicException('Deprecated, use Fractal::create() or response()->json() instead');
    }

    public function fractal()
    {
        return Fractal::create();
    }
}
