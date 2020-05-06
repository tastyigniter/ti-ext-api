<?php namespace Igniter\Api\ApiResources;

use Igniter\Api\Classes\ApiController;

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
	       'menu_options'
        ],
        'model' => \Admin\Models\Menus_model::class,
        'transformer' => \Igniter\Api\ApiResources\Transformers\MenuTransformer::class,
    ];
}