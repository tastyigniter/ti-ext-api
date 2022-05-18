<?php

namespace Igniter\Api\ApiResources\Transformers;

use Igniter\Admin\Models\Customer;
use League\Fractal\TransformerAbstract;

class CustomerTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'addresses',
        'orders',
        'reservations',
    ];

    public function transform(Customer $customer)
    {
        return $customer->toArray();
    }

    public function includeAddresses(Customer $customer)
    {
        return $this->collection($customer->addresses, new AddressTransformer, 'addresses');
    }

    public function includeOrders(Customer $customer)
    {
        return $this->collection($customer->orders, new OrderTransformer, 'orders');
    }

    public function includeReservations(Customer $customer)
    {
        return $this->collection($customer->reservations, new ReservationTransformer, 'reservations');
    }
}
