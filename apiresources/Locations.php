<?php namespace Igniter\Api\ApiResources;

use Igniter\Api\Classes\ApiController;
use Igniter\Api\Classes\ApiManager;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Locations API Controller
 */
class Locations extends ApiController
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
        	'working_hours', 
        	'delivery_areas',
        	'reviews'
        ],
        'model' => \Admin\Models\Locations_model::class,
        'transformer' => \Igniter\Api\ApiResources\Transformers\LocationTransformer::class,
    ];
    
    protected $requiredAbilities = ['locations:*'];
    
}