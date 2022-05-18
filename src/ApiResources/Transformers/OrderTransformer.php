<?php

namespace Igniter\Api\ApiResources\Transformers;

use Igniter\Admin\Models\Order;
use League\Fractal\TransformerAbstract;

class OrderTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'customer',
        'location',
        'address',
        'payment_method',
        'status',
        'status_history',
        'assignee',
        'assignee_group',
    ];

    public function transform(Order $order)
    {
        return array_merge($order->toArray(), [
            'currency' => app('currency')->getDefault()->currency_code,
            'order_totals' => $order->getOrderTotals(),
            'order_menus' => $order->getOrderMenusWithOptions(),
        ]);
    }

    public function includeCustomer(Order $order)
    {
        if (!$order->customer)
            return;

        return $this->item($order->customer, new CustomerTransformer, 'customers');
    }

    public function includeLocation(Order $order)
    {
        return $this->item($order->location, new LocationTransformer, 'locations');
    }

    public function includeAddress(Order $order)
    {
        if (!$order->address)
            return;

        return $this->item($order->address, new AddressTransformer, 'addresses');
    }

    public function includePaymentMethod(Order $order)
    {
        if (!$order->payment_method)
            return;

        return $this->item($order->payment_method, new PaymentMethodTransformer, 'payment_methods');
    }

    public function includeStatus(Order $order)
    {
        return $this->item($order->status, new StatusTransformer, 'statuses');
    }

    public function includeStatusHistory(Order $order)
    {
        return $this->collection($order->status_history, new StatusHistoryTransformer, 'status_history');
    }

    public function includeAssignee(Order $order)
    {
        if (!$order->assignee)
            return;

        return $this->item($order->assignee, new StaffTransformer, 'staff');
    }

    public function includeAssigneeGroup(Order $order)
    {
        if (!$order->assignee_group)
            return;

        return $this->item($order->assignee_group, new StaffGroupTransformer, 'staff_group');
    }
}
