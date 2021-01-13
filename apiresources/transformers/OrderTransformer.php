<?php

namespace Igniter\Api\ApiResources\Transformers;

use Admin\Models\Orders_model;
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

    public function transform(Orders_model $order)
    {
        return array_merge($order->toArray(), [
            'order_total_as_currency' => currency_format($order->order_total),
            'order_totals' => $order->getOrderTotals()->map(function ($total){
                $total->value_as_currency = currency_format($total->value);
                return $total;
            }),
            'order_menus' => $order->getOrderMenus()->map(function ($menu){
                $menu->option_values = unserialize($menu->option_values);
                return $menu;
            }),
        ]);
    }

    public function includeCustomer(Orders_model $order)
    {
        if (!$order->customer) 
            return;
        
        return $this->item($order->customer, new CustomerTransformer, 'customers');
    }

    public function includeLocation(Orders_model $order)
    {
        return $this->item($order->location, new LocationTransformer, 'locations');
    }

    public function includeAddress(Orders_model $order)
    {
        if (!$order->address) 
            return;

        return $this->item($order->address, new AddressTransformer, 'addresses');
    }

    public function includePaymentMethod(Orders_model $order)
    {
        if (!$order->payment_method) 
            return;

        return $this->item($order->payment_method, new PaymentMethodTransformer, 'payment_methods');
    }

    public function includeStatus(Orders_model $order)
    {
        return $this->item($order->status, new StatusTransformer, 'statuses');
    }

    public function includeStatusHistory(Orders_model $order)
    {
        return $this->collection($order->status_history, new StatusHistoryTransformer, 'status_history');
    }

    public function includeAssignee(Orders_model $order)
    {
        if (!$order->assignee) 
            return;

        return $this->item($order->assignee, new StaffTransformer, 'staff');
    }

    public function includeAssigneeGroup(Orders_model $order)
    {
        if (!$order->assignee_group) 
            return;
        
        return $this->item($order->assignee_group, new StaffGroupTransformer, 'staff_group');
    }
}
