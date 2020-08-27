<?php

namespace Igniter\Api\ApiResources\Transformers;

use Igniter\Api\Classes\TransformerAbstract;

class OrderTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'customer',
        'location',
        'address',
        'payment_method',
        'status',
        'assignee',
        'assignee_group',
        'status_history',
    ];

    public function toArray($request)
    {
        return array_merge(parent::toArray($request), [
            'customer' => $this->whenLoaded('customer'),
            'location' => $this->whenLoaded('location'),
            'address' => $this->whenLoaded('address'),
            'payment_method' => $this->whenLoaded('payment_method'),
            'status' => $this->whenLoaded('status'),
            'assignee' => $this->whenLoaded('assignee'),
            'assignee_group' => $this->whenLoaded('assignee_group'),
            'status_history' => $this->whenLoaded('status_history'),
        ]);
    }
}
