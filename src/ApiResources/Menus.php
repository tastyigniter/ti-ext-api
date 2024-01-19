<?php

namespace Igniter\Api\ApiResources;

use Igniter\Api\Classes\ApiController;

/**
 * Menus API Controller
 */
class Menus extends ApiController
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
        'request' => \Igniter\Cart\Requests\MenuRequest::class,
        'repository' => Repositories\MenuRepository::class,
        'transformer' => Transformers\MenuTransformer::class,
    ];

    protected string|array $requiredAbilities = ['menus:*'];
}
