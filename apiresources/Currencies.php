<?php

namespace Igniter\Api\ApiResources;

use Igniter\Api\Classes\ApiController;

/**
 * Currencies API Controller
 */
class Currencies extends ApiController
{
    public $implement = ['Igniter.Api.Actions.RestController'];

    public $restConfig = [
        'actions' => [
            'index' => [
                'pageLimit' => 20,
            ],
        ],
        'request' => \System\Requests\Currency::class,
        'repository' => Repositories\CurrencyRepository::class,
        'transformer' => Transformers\CurrencyTransformer::class,
    ];

    protected $requiredAbilities = ['currencies:*'];
}
