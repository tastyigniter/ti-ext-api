<?php

namespace Igniter\Api\ApiResources\Transformers;

use Igniter\Api\Traits\MergesIdAttribute;
use Igniter\Reservation\Models\Reservation;
use League\Fractal\TransformerAbstract;

class ReservationTransformer extends TransformerAbstract
{
    use MergesIdAttribute;

    protected array $availableIncludes = [
        'location',
        'tables',
        'status',
        'status_history',
        'assignee',
        'assignee_group',
    ];

    public function transform(Reservation $reservation)
    {
        return $this->mergesIdAttribute($reservation);
    }

    public function includeLocation(Reservation $reservation)
    {
        return $this->item($reservation->location, new LocationTransformer, 'locations');
    }

    public function includeTables(Reservation $reservation)
    {
        return $this->collection($reservation->tables, new DiningTableTransformer, 'tables');
    }

    public function includeStatus(Reservation $reservation)
    {
        return $this->item($reservation->status, new StatusTransformer, 'statuses');
    }

    public function includeStatusHistory(Reservation $reservation)
    {
        return $this->collection($reservation->status_history, new StatusHistoryTransformer, 'status_history');
    }

    public function includeAssignee(Reservation $reservation)
    {
        return $this->item($reservation->assignee, new UserTransformer, 'assignee');
    }

    public function includeAssigneeGroup(Reservation $reservation)
    {
        return $this->item($reservation->assignee_group, new UserGroupTransformer, 'assignee_group');
    }
}
