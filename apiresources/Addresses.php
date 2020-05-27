<?php namespace Igniter\Api\ApiResources;

use Igniter\Api\Classes\ApiController;
use Igniter\Api\Classes\ApiManager;

/**
 * Addresses API Controller
 */
class Addresses extends ApiController
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
        'relations' => [],
        'model' => \Admin\Models\Addresses_model::class,
        'transformer' => \Igniter\Api\ApiResources\Transformers\AddressTransformer::class,
    ];
        
    public function restExtendModel($query){
	    
	    if ($this->action == 'store')
	    {
		    
        	if (!ApiManager::instance()->currentAccessTokenIsAdmin())
			{
				$token = ApiManager::instance()->currentAccessToken();
				return $query->where('customer_id', $token->tokenable_id);
			}
		
		}
	    
    }
    
    public function restExtendQuery($query){
	    	    
		if (!ApiManager::instance()->currentAccessTokenIsAdmin())
		{
			$token = ApiManager::instance()->currentAccessToken();
			return $query->where('customer_id', $token->tokenable_id);
		}
	    
    }  

}