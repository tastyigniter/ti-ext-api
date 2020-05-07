<?php namespace Igniter\Api\ApiResources;

use Igniter\Api\Classes\ApiController;

/**
 * Orders API Controller
 */
class Orders extends ApiController
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
	       'payment_logs',
	       'coupon_history'
        ],
        'model' => \Admin\Models\Orders_model::class,
        'transformer' => \Igniter\Api\ApiResources\Transformers\OrderTransformer::class,
    ];
}