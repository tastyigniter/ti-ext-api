<?php namespace Igniter\Api\ApiResources\Transformers;

use Igniter\Api\Classes\TransformerAbstract;

class CustomerTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'addresses',
        'orders',
        'reservations',
    ];

    public function toArray($request)
    {
        return array_merge(parent::toArray($request), [
            'addresses' => $this->whenLoaded('addresses'),
            'orders' => $this->whenLoaded('orders'),
            'reservations' => $this->whenLoaded('reservations'),
        ]);
    }
}