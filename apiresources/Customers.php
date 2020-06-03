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
        'model' => \Admin\Models\Customers_model::class,
        'transformer' => \Igniter\Api\ApiResources\Transformers\CustomerTransformer::class,
        'authorization' => ['index:admin', 'store:users', 'show:admin', 'update:users', 'destroy:admin'],
    ];
    
    protected $requiredAbilities = ['customers:*'];
        
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