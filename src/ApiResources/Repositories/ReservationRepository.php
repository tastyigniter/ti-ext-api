<?php

namespace Igniter\Api\ApiResources\Repositories;

use Igniter\Api\Classes\AbstractRepository;
use Igniter\Reservation\Models\Reservation;

class ReservationRepository extends AbstractRepository
{
    protected $modelClass = Reservation::class;

    protected static $locationAwareConfig = [];

    protected static $customerAwareConfig = [];
}
