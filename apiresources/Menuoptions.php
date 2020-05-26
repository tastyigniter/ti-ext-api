<?php namespace Igniter\Api\ApiResources;

use Igniter\Api\Classes\ApiController;
use Igniter\Api\Classes\ApiManager;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Menuoptions API Controller
 */
class MenuOptions extends ApiController
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
	       'menu_options',
	       'option_values'
        ],
        'model' => \Admin\Models\Menu_options_model::class,
        'transformer' => \Igniter\Api\ApiResources\Transformers\MenuOptionsTransformer::class,
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