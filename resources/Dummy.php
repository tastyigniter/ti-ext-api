<?php namespace Igniter\Api\Resources;

use Igniter\Api\Classes\ApiController;

/**
 * Dummy API Controller
 */
class Dummy extends ApiController
{
    public $implement = [
        'Igniter.Api.Actions.RestController'
    ];

    public $restConfig = [
        'actions' => [
            'index' => [
                'pageSize' => 20,
            ],
            'store' => [],
            'show' => [],
            'update' => [],
            'destroy' => [],
        ],
        'relations' => [],
        'model' => 'igniter\api\Models\Resource',
        'transformer' => 'Igniter\Api\Resources\Transformers\DummyTransformer',
    ];
}