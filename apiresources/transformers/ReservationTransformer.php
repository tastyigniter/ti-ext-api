<?php

namespace Igniter\Api\ApiResources\Transformers;

use Admin\Models\Reservations_model;
use League\Fractal\TransformerAbstract;

class ReservationTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'location',
        'tables',
        'status',
        'status_history',
        'assignee',
        'assignee_group',
    ];

    public function transform(Reservations_model $reservation)
    {
        return $reservation->toArray();
    }

    public function includeLocation(Reservations_model $reservation)
    {
        return $this->item($reservation->location, new LocationTransformer, 'locations');
    }

    public function includeTables(Reservations_model $reservation)
    {
        return $this->collection($reservation->tables, new TableTransformer, 'tables');
    }

    public function includeStatus(Reservations_model $reservation)
    {
        return $this->item($reservation->status, new StatusTransformer, 'statuses');
    }

    public function includeStatusHistory(Reservations_model $reservation)
    {
        return $this->collection($reservation->status_history, new StatusHistoryTransformer, 'status_history');
    }

    public function includeAssignee(Reservations_model $reservation)
    {
        return $this->item($reservation->assignee, new StaffTransformer, 'staff');
    }

    public function includeAssigneeGroup(Reservations_model $reservation)
    {
        return $this->item($reservation->assignee_group, new StaffGroupTransformer, 'staff_group');
    }
}
