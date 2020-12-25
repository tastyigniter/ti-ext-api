<?php

namespace Igniter\Api\ApiResources;

use Igniter\Api\Classes\ApiController;

/**
 * Menus API Controller
 */
class Menus extends ApiController
{
    public $implement = ['Igniter.Api.Actions.RestController'];

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
        'request' => \Admin\Requests\Menu::class,
        'repository' => Repositories\MenuRepository::class,
        'transformer' => Transformers\MenuTransformer::class,
    ];

    protected $requiredAbilities = ['menus:*'];
}
