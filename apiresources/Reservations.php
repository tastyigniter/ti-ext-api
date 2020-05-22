<?php namespace Igniter\Api\ApiResources;

use Igniter\Api\Classes\ApiController;
use Igniter\Api\Classes\ApiManager;

/**
 * Reservations API Controller
 */
class Reservations extends ApiController
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
        'model' => \Admin\Models\Reservations_model::class,
        'transformer' => \Igniter\Api\ApiResources\Transformers\ReservationTransformer::class,
    ];
    
    public function restExtendModel($query){
	    
		$token = ApiManager::instance()->currentAccessToken();
		if ($token->tokenable_type == 'users'){
			return $query->where('customer_id', $token->tokenable_id);
		}
	    
    }
    
}