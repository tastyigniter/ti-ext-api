<?php namespace Igniter\Api\ApiResources;

use Igniter\Api\Classes\ApiController;
use Igniter\Api\Classes\ApiManager;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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

    protected $requiredAbilities = ['reservations:*'];
}