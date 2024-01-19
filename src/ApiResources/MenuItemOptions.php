<?php

namespace Igniter\Api\ApiResources;

use Igniter\Api\Classes\ApiController;

/**
 * Options API Controller
 */
class MenuItemOptions extends ApiController
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
        'request' => Requests\MenuItemOptionRequest::class,
        'repository' => Repositories\MenuItemOptionRepository::class,
        'transformer' => Transformers\MenuItemOptionTransformer::class,
    ];

    protected string|array $requiredAbilities = ['menu_item_options:*'];
}
