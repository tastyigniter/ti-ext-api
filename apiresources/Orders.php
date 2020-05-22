<?php namespace Igniter\Api\ApiResources;

use Igniter\Api\Classes\ApiController;
use Igniter\Api\Classes\ApiManager;

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
    
    public function restExtendModel($query){
	    
		$token = ApiManager::instance()->currentAccessToken();
		if ($token->tokenable_type == 'users'){
			return $query->where('customer_id', $token->tokenable_id);
		}
	    
    }
    
}