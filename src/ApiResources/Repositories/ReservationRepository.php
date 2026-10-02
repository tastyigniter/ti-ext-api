<?php

declare(strict_types=1);

namespace Igniter\Api\ApiResources\Repositories;

use Igniter\Api\Classes\AbstractRepository;
use Igniter\Api\Traits\AppliesAssigneeScope;
use Igniter\Reservation\Models\Reservation;

class ReservationRepository extends AbstractRepository
{
    use AppliesAssigneeScope;

    protected ?string $modelClass = Reservation::class;

    protected static $locationAwareConfig = [];

    protected static $customerAwareConfig = [];
}
