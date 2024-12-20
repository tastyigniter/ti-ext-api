<?php

namespace Igniter\Api\ApiResources\Transformers;

use Igniter\Api\Traits\MergesIdAttribute;
use Igniter\Cart\Models\Order;
use League\Fractal\TransformerAbstract;

class OrderTransformer extends TransformerAbstract
{
    use MergesIdAttribute;

    protected array $availableIncludes = [
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
        return $this->mergesIdAttribute($order, [
            'currency' => app('currency')->getDefault()->currency_code,
            'order_totals' => $order->getOrderTotals(),
            'order_menus' => $order->getOrderMenusWithOptions(),
        ]);
    }

    public function includeCustomer(Order $order)
    {
        return $order->customer ? $this->item($order->customer, new CustomerTransformer, 'customers') : null;
    }

    public function includeLocation(Order $order)
    {
        return $this->item($order->location, new LocationTransformer, 'locations');
    }

    public function includeAddress(Order $order)
    {
        return $order->address ? $this->item($order->address, new AddressTransformer, 'addresses') : null;
    }

    public function includePaymentMethod(Order $order)
    {
        return $order->payment_method ? $this->item($order->payment_method, new PaymentMethodTransformer, 'payment_methods') : null;
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
        return $order->assignee ? $this->item($order->assignee, new UserTransformer, 'assignee') : null;
    }

    public function includeAssigneeGroup(Order $order)
    {
        return $order->assignee_group ? $this->item($order->assignee_group, new UserGroupTransformer, 'assignee_group') : null;
    }
}
