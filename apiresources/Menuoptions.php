<?php namespace Igniter\Api\ApiResources;

use Igniter\Api\Classes\ApiController;

/**
 * Menuoptions API Controller
 */
class Menuoptions extends ApiController
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
        'transformer' => \Igniter\Api\ApiResources\Transformers\MenuoptionTransformer::class,
    ];
}