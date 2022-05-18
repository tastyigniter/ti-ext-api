<?php

namespace Igniter\Api\ApiResources;

use Igniter\Api\Classes\ApiController;

/**
 * Reservations API Controller
 */
class Reservations extends ApiController
{
    public $implement = ['Igniter.Api.Actions.RestController'];

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
        'request' => Requests\ReservationRequest::class,
        'repository' => Repositories\ReservationRepository::class,
        'transformer' => Transformers\ReservationTransformer::class,
    ];

    protected $requiredAbilities = ['reservations:*'];
}
