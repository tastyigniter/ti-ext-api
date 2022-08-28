<?php

namespace Igniter\Api\ApiResources;

use Igniter\Api\Classes\ApiController;

/**
 * Reservations API Controller
 */
class Reservations extends ApiController
{
    public $implement = [\Igniter\Api\Http\Actions\RestController::class];

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

    public function restExtendQuery($query)
    {
        if (($token = $this->getToken()) && $token->isForCustomer())
            $query->where('customer_id', $token->tokenable_id);

        return $query;
    }
}
