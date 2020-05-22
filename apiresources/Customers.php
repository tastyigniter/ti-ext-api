<?php namespace Igniter\Api\ApiResources;

use Igniter\Api\Classes\ApiController;
use Igniter\Api\Classes\ApiManager;

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
    
    public function restExtendModel($query){
	    
		$token = ApiManager::instance()->currentAccessToken();
		if ($token->tokenable_type == 'users'){
			return $query->where('customer_id', $token->tokenable_id);
		}
	    
    }
    
}