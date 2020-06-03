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
        'relations' => [],
        'model' => \Admin\Models\Orders_model::class,
        'transformer' => \Igniter\Api\ApiResources\Transformers\OrderTransformer::class,
        'authorization' => ['index:users', 'store:users', 'show:users', 'update:admin', 'destroy:admin'],
    ];

    protected $requiredAbilities = ['orders:*'];
        
    public function restExtendQuery($query)
    {
	    
        if (($token = $this->getToken()) && $token->isForCustomer())
            $query->where('customer_id', $token->tokenable_id);

		return $query;
	    
    }
    
    public function store()
    {
	    
        if (($token = $this->getToken()) && $token->isForCustomer())
            Request::merge(['customer_id' => $token->tokenable_id]);
		
		$this->asExtension('RestController')->store();
	    
    }
}