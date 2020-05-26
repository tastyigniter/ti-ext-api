<?php namespace Igniter\Api\ApiResources;

use Igniter\Api\Classes\ApiController;
use Igniter\Api\Classes\ApiManager;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Categories API Controller
 */
class Categories extends ApiController
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
        'model' => \Admin\Models\Categories_model::class,
        'transformer' => \Igniter\Api\ApiResources\Transformers\CategoryTransformer::class,
    ];
    
    public function store()
    {
	    
        if (!ApiManager::instance()->currentAccessTokenIsAdmin())
	       throw new BadRequestHttpException;
		
        parent::store();  
	    
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