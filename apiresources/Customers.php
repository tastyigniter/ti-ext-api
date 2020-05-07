<?php namespace Igniter\Api\ApiResources;

use Igniter\Api\Classes\ApiController;

/**
 * Customers API Controller
 */
class Customers extends ApiController
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
        'relations' => [
	       'addresses'
        ],
        'model' => \Admin\Models\Customers_model::class,
        'transformer' => \Igniter\Api\ApiResources\Transformers\CustomerTransformer::class,
    ];
}