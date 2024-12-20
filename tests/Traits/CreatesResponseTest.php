<?php

namespace Igniter\Api\Tests\Traits;

use Igniter\Api\Traits\CreatesResponse;
use Spatie\Fractal\Fractal;

it('throws LogicException when response method is called', function() {
    $traitObject = new class
    {
        use CreatesResponse;
    };

    expect(function() use ($traitObject) {
        $traitObject->response();
    })->toThrow(\LogicException::class, 'Deprecated, use Fractal::create() or response()->json() instead');
});

it('returns a Fractal instance from fractal method', function() {
    $traitObject = new class
    {
        use CreatesResponse;
    };

    $result = $traitObject->fractal();

    expect($result)->toBeInstanceOf(Fractal::class);
});
