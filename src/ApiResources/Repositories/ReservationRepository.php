<?php

namespace Igniter\Api\ApiResources\Repositories;

use Igniter\Admin\Models\Reservation;
use Igniter\Api\Classes\AbstractRepository;

class ReservationRepository extends AbstractRepository
{
    protected $modelClass = Reservation::class;
}
