<?php namespace Igniter\Api\ApiResources;

use Igniter\Api\Classes\ApiController;
use Igniter\Api\Classes\ApiManager;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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
	       'addresses',
	       'orders',
	       'reservations'
        ],
        'model' => \Admin\Models\Customers_model::class,
        'transformer' => \Igniter\Api\ApiResources\Transformers\CustomerTransformer::class,
    ];
        
    protected $requiredAbilities = ['customers:*']; 
    
}