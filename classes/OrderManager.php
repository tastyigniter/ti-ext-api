<?php

namespace Igniter\Api\Classes;

use Igniter\Cart\Classes\OrderManager;

class APIOrderManager extends OrderManager
{
    public function saveOrder($order, array $data)
    {
        Event::fire('igniter.checkout.beforeSaveOrder', [$order, $data]);

        if ($this->customer) {
            $data['email'] = $this->customer->email;
        }

        $addressId = null;
        if ($address = array_get($data, 'address', [])) {
            $address['customer_id'] = $this->getCustomerId();

            $addressId = array_get($data, 'address_id');
            $addressId = !empty($addressId) ? $addressId : Addresses_model::createOrUpdateFromRequest($address)->getKey();

            // Update customer default address
            if ($this->customer) {
                $this->customer->address_id = $addressId;
                $this->customer->save();
            }
        }

        $menuItems = $this->getMenuItemsAndSetTotals($data);

        $order->fill($data);
        $order->address_id = $addressId;
        $this->applyRequiredAttributes($order);
        $order->save();

        $this->setCurrentOrderId($order->order_id);

        $order->addOrderMenus($menuItems);
        $order->addOrderTotals(array_get($data, 'cart_totals'));

        // Lets log the coupon so we can redeem it later
        // todo: cart conditions

        return $order;
    }

    private function getMenuItemsAndSetTotals(&$data)
    {
        $total = 0;
        $totalItems = 0;
        $menuItems = [];

        foreach (array_get($data, 'menu_items', []) as $item) {
            $item['rowId'] = md5(uniqid());
            $menuItems[] = $item;

            $total += $item['price']*$item['qty'];
            $totalItems += $item['qty'];
            if (!empty($item['options'])) {
                foreach ($item['options'] as $option) {
                    $total += $option['price']*$option['qty'];
                }
            }
        }

        $data['total_items'] = $totalItems;
        $data['order_total'] = $total;

        return $menuItems;
    }
}
