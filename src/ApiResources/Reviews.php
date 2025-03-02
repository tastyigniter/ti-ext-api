<?php

namespace Igniter\Api\ApiResources;

use Igniter\Api\Classes\ApiController;

/**
 * Reviews API Controller
 */
class Reviews extends ApiController
{
    public $implement = [\Igniter\Api\Http\Actions\RestController::class];

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
        'request' => \Igniter\Local\Requests\Review::class,
        'repository' => Repositories\ReviewRepository::class,
        'transformer' => Transformers\ReviewTransformer::class,
    ];

    protected $requiredAbilities = ['reviews:*'];
}
