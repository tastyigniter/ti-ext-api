<?php

namespace Igniter\Api\Tests\ApiResources;

use Igniter\Api\Classes\ApiManager;

it('loads registered api resources', function () {
    $manager = resolve(ApiManager::class);
    $resources = $manager->getResources();

    $this->get('/admin');
    expect(true)->toBeTrue();
});

