<?php

namespace Igniter\Api\ApiResources;

use Igniter\Api\Classes\ApiController;

/**
 * Options API Controller
 */
class MenuOptions extends ApiController
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
        'request' => Requests\MenuOptionRequest::class,
        'repository' => Repositories\MenuOptionRepository::class,
        'transformer' => Transformers\MenuOptionTransformer::class,
    ];

    protected string|array $requiredAbilities = ['menu_options:*'];
}
