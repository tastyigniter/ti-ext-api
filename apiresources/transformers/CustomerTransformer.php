<?php

namespace Igniter\Api\ApiResources\Transformers;

use Admin\Models\Customers_model;
use League\Fractal\TransformerAbstract;

class CustomerTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'addresses',
        'orders',
        'reservations',
    ];

    public function transform(Customers_model $customer)
    {
        return $customer->toArray();
    }

    public function includeAddresses(Customers_model $customer)
    {
        return $this->collection($customer->addresses, new AddressTransformer, 'addresses');
    }

    public function includeOrders(Customers_model $customer)
    {
        return $this->collection($customer->orders, new OrderTransformer, 'orders');
    }

    public function includeReservations(Customers_model $customer)
    {
        return $this->collection($customer->reservations, new ReservationTransformer, 'reservations');
    }
}
