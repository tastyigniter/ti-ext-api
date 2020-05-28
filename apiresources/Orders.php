<?php namespace Igniter\Api\ApiResources;

use Igniter\Api\Classes\ApiController;
use Igniter\Api\Classes\ApiManager;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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
	    
	    if ($this->action == 'store')
	    {
		    
        	if (!ApiManager::instance()->currentAccessTokenIsAdmin())
			{
				$token = ApiManager::instance()->currentAccessToken();
				\Request::merge(['customer_id' => $token->tokenable_id]);
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
    
    public function update()
    {
	    
        if (!ApiManager::instance()->currentAccessTokenIsAdmin())
	       throw new BadRequestHttpException;
		
		$this->asExtension('RestController')->update();
	    
    }
    
    public function destroy()
    {
	    
        if (!ApiManager::instance()->currentAccessTokenIsAdmin())
	       throw new BadRequestHttpException;
		
		$this->asExtension('RestController')->destroy();
	    
    }
       
}