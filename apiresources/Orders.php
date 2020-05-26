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
    
    public $alreadyRestExtended = false;
    
    public function restExtendModel($query){
	    
	    if (!$this->alreadyRestExtended)
	    {
		    
			$token = ApiManager::instance()->currentAccessToken();
			if ($token !== NULL && $token->tokenable_type == 'customers')
			{
				return $query->where('customer_id', $token->tokenable_id);
			}
		
		}
	    
    }
    
    public function restExtendQuery($query){
	    
	    $this->alreadyRestExtended = true;
	    
		$token = ApiManager::instance()->currentAccessToken();
		if ($token !== NULL && $token->tokenable_type == 'customers')
		{
			return $query->where('customer_id', $token->tokenable_id);
		}
	    
    } 
    
    public function update()
    {
	    
        if (!ApiManager::instance()->currentAccessTokenIsAdmin())
	       throw new BadRequestHttpException;
		
        parent::update();  
	    
    }
    
    public function destroy()
    {
	    
        if (!ApiManager::instance()->currentAccessTokenIsAdmin())
	       throw new BadRequestHttpException;
		
        parent::destroy();  
	    
    }
       
}