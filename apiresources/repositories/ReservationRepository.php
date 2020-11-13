<?php

namespace Igniter\Api\ApiResources\Repositories;

use Admin\Models\Customers_model;
use Admin\Models\Locations_model;
use Admin\Models\Menus_model;
use Admin\Models\Orders_model;
use Admin\Models\Reservations_model;
use Igniter\Api\Classes\AbstractRepository;

class ReservationRepository extends AbstractRepository
{
    protected $modelClass = Reservations_model::class;
}