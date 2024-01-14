<?php

namespace Igniter\Api\ApiResources;

use Igniter\Api\Classes\ApiController;

/**
 * Tables API Controller
 */
class DiningTables extends ApiController
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
        'request' => \Igniter\Reservation\Requests\DiningTableRequest::class,
        'repository' => Repositories\DiningTableRepository::class,
        'transformer' => Transformers\DiningTableTransformer::class,
    ];

    protected $requiredAbilities = ['tables:*'];
}
