<?php

declare(strict_types=1);

namespace Igniter\Api\Tests\Fixtures;

use Igniter\Api\Classes\ApiController;

class TestResource extends ApiController
{
    public array $implement = [\Igniter\Api\Http\Actions\RestController::class];

    public array $restConfig = [
        'actions' => [],
        'repository' => TestRepository::class,
        'transformer' => TestTransformer::class,
    ];
}
