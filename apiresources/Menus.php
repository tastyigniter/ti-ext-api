<?php namespace Igniter\Api\ApiResources;

use Igniter\Api\Classes\ApiController;
use Igniter\Api\Classes\ApiManager;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Menus API Controller
 */
class Menus extends ApiController
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
	       'categories',
	       'menu_options.menu_option_values',
	       'menu_options.option_values'
        ],
        'model' => \Admin\Models\Menus_model::class,
        'transformer' => \Igniter\Api\ApiResources\Transformers\MenuTransformer::class,
    ];
    
    public function store()
    {
	    
        if (!ApiManager::instance()->currentAccessTokenIsAdmin())
	       throw new BadRequestHttpException;
		
		$this->asExtension('RestController')->store();
	    
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