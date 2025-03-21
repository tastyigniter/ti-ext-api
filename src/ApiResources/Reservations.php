<?php

declare(strict_types=1);

namespace Igniter\Api\ApiResources;

use Igniter\Api\Classes\ApiController;

/**
 * Reservations API Controller
 */
class Reservations extends ApiController
{
    public array $implement = [\Igniter\Api\Http\Actions\RestController::class];

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

    protected string|array $requiredAbilities = ['reservations:*'];
}
