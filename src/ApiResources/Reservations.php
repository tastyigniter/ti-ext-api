<?php

declare(strict_types=1);

namespace Igniter\Api\ApiResources;

use Igniter\Api\ApiResources\Repositories\ReservationRepository;
use Igniter\Api\ApiResources\Requests\ReservationRequest;
use Igniter\Api\ApiResources\Transformers\ReservationTransformer;
use Igniter\Api\Classes\ApiController;
use Igniter\Api\Http\Actions\RestController;

/**
 * Reservations API Controller
 */
class Reservations extends ApiController
{
    public array $implement = [RestController::class];

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
        'request' => ReservationRequest::class,
        'repository' => ReservationRepository::class,
        'transformer' => ReservationTransformer::class,
    ];

    protected string|array $requiredAbilities = ['reservations:*'];
}
