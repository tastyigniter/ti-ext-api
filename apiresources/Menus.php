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
        'model' => \Admin\Models\Menus_model::class,
        'transformer' => \Igniter\Api\ApiResources\Transformers\MenuTransformer::class,
    ];
    
    protected $requiredAbilities = ['menus:*'];
        
}