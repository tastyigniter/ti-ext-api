<?php

namespace Igniter\Api\ApiResources\Repositories;

use Admin\Models\Reservations_model;
use Igniter\Api\Classes\AbstractRepository;

class ReservationRepository extends AbstractRepository
{
    protected $modelClass = Reservations_model::class;

    protected static $locationAwareConfig = [];

    protected static $customerAwareConfig = [];
}
