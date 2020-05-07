<?php namespace Igniter\Api\ApiResources;

use Igniter\Api\Classes\ApiController;

/**
 * Locations API Controller
 */
class Locations extends ApiController
{
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
        'relations' => ['working_hours', 'delivery_areas'],
        'model' => \Admin\Models\Locations_model::class,
        'transformer' => \Igniter\Api\ApiResources\Transformers\LocationTransformer::class,
    ];
}