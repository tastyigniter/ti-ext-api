<?php

declare(strict_types=1);

namespace Igniter\Api\ApiResources;

use Igniter\Api\Classes\ApiController;

/**
 * Reviews API Controller
 */
class Reviews extends ApiController
{
    public array $implement = [\Igniter\Api\Http\Actions\RestController::class];

    public $restConfig = [
        'actions' => [
            'index' => [
                'pageLimit' => 20,
            ],
            'store' => [],
            'show' => [],
            'update' => [],
            'destroy' => [],
        ],
        'request' => \Igniter\Local\Http\Requests\ReviewRequest::class,
        'repository' => Repositories\ReviewRepository::class,
        'transformer' => Transformers\ReviewTransformer::class,
    ];

    protected string|array $requiredAbilities = ['reviews:*'];
}
