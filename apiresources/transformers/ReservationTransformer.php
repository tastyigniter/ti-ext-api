<?php

namespace Igniter\Api\ApiResources\Transformers;

use Igniter\Api\Classes\TransformerAbstract;

class ReservationTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'location',
        'tables',
        'status',
        'assignee',
        'assignee_group',
        'status_history',
    ];

    public function toArray($request)
    {
        return array_merge(parent::toArray($request), [
            'location' => $this->whenLoaded('location'),
            'tables' => $this->whenLoaded('tables'),
            'status' => $this->whenLoaded('status'),
            'assignee' => $this->whenLoaded('assignee'),
            'assignee_group' => $this->whenLoaded('assignee_group'),
            'status_history' => $this->whenLoaded('status_history'),
        ]);
    }
}
