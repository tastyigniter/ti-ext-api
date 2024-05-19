<?php

namespace Igniter\Api\Tests;

use Igniter\Api\Classes\Fractal;
use Igniter\Api\Models\Resource;

it('loads registered api resources', function() {
    $resources = Resource::listRegisteredResources();

    expect($resources)->toHaveKey('categories');
});

it('replaces fractal.fractal_class config item', function() {
    expect(config('fractal.fractal_class'))->toBe(Fractal::class);
});

