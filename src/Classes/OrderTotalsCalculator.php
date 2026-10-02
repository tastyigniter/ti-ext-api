<?php

declare(strict_types=1);

namespace Igniter\Api\Classes;

use Igniter\Cart\Cart;
use Igniter\Cart\CartCondition;
use Igniter\Cart\CartItem;
use Igniter\Cart\Classes\CartConditionManager;
use Igniter\Cart\Models\CartSettings;
use Igniter\Cart\Models\Menu;
use Igniter\Cart\Models\MenuItemOption;
use Igniter\Cart\Models\MenuItemOptionValue;
use Igniter\Flame\Exception\ApplicationException;
use Igniter\Local\Facades\Location;
use Igniter\Local\Models\Location as LocationModel;
use Illuminate\Support\Collection;

class OrderTotalsCalculator
{
    /**
     * @param  list<array<string, mixed>>  $orderMenus
     * @return array{
     *     order_total: float,
     *     order_menus: list<array<string, mixed>>,
     *     order_totals: list<array{code: string, title: string, value: float, priority: int, is_summable: bool}>
     * }
     */
    public function calculate(int $locationId, string $orderType, array $orderMenus): array
    {
        $location = LocationModel::query()->find($locationId);
        throw_unless($location instanceof LocationModel, new ApplicationException('Location not found.'));

        Location::setCurrent($location);
        Location::updateOrderType($orderType);

        /** @var Cart $cart */
        $cart = resolve('cart');
        $previousInstance = $cart->currentInstance();
        $instance = 'api-totals-'.uniqid('', true);
        $cart->instance($instance);

        try {
            $this->loadCartConditions($cart);

            $menuTotals = [];
            foreach ($orderMenus as $orderMenu) {
                $menuTotals[] = $this->addMenuToCart($cart, $orderMenu, $location);
            }

            $totals = $this->buildOrderTotals($cart);

            return [
                'order_total' => (float)($totals['total']['value'] ?? 0),
                'order_menus' => $menuTotals,
                'order_totals' => array_values($totals),
            ];
        } finally {
            $cart->destroy();
            $cart->instance($previousInstance);
        }
    }

    protected function loadCartConditions(Cart $cart): void
    {
        $conditionManager = resolve(CartConditionManager::class);
        $conditions = CartSettings::instance()->get('conditions') ?: [];

        foreach ($conditions as $definition) {
            if (!(bool)array_get($definition, 'status', true)) {
                continue;
            }

            $definition['cartInstance'] = $cart->currentInstance();
            $className = (string)array_get($definition, 'className', '');
            if ($className === '') {
                continue;
            }

            $cart->loadCondition($conditionManager->makeCondition($className, $definition));
        }
    }

    /**
     * @param  array<string, mixed>  $orderMenu
     * @return array<string, mixed>
     */
    protected function addMenuToCart(Cart $cart, array $orderMenu, LocationModel $location): array
    {
        $menuId = (int)($orderMenu['id'] ?? 0);
        $qty = max(1, (int)($orderMenu['qty'] ?? 1));
        $comment = isset($orderMenu['comment']) ? (string)$orderMenu['comment'] : null;
        $lineId = isset($orderMenu['line_id']) ? (string)$orderMenu['line_id'] : null;

        $menu = Menu::findBy($menuId, $location);
        throw_unless($menu instanceof Menu, new ApplicationException(sprintf('Menu item %s was not found.', $menuId)));

        $options = $this->cartOptionsForMenu($menu, (array)($orderMenu['options'] ?? []));
        /** @var CartItem $cartItem */
        $cartItem = $cart->add($menu, $qty, $options, $comment);

        $unitWithoutConditions = $cartItem->qty > 0
            ? (float)$cartItem->subtotalWithoutConditions() / (float)$cartItem->qty
            : 0.0;
        $unitWithConditions = $cartItem->qty > 0
            ? (float)$cartItem->subtotal() / (float)$cartItem->qty
            : 0.0;
        $hasConditions = $cartItem->hasConditions() > 0;

        return array_filter([
            'line_id' => $lineId,
            'id' => $menuId,
            'name' => (string)$cartItem->name,
            'qty' => $qty,
            'price' => round($unitWithoutConditions, 4),
            'subtotalWithoutConditions' => round($unitWithoutConditions * $qty, 4),
            'subtotal' => round($unitWithConditions * $qty, 4),
            'hasConditions' => $hasConditions,
            'comment' => $comment ?? '',
            'options' => $this->serializeCartItemOptions($cartItem),
        ], fn(mixed $value): bool => $value !== null);
    }

    /**
     * @return list<array{id: int, name: string, values: list<array{id: int, name: string, price: float, qty: int}>}>
     */
    protected function serializeCartItemOptions(CartItem $cartItem): array
    {
        return $cartItem->options->map(fn($option): array => [
            'id' => (int)$option->id,
            'name' => (string)$option->name,
            'values' => $option->values->map(fn($value): array => [
                'id' => (int)$value->id,
                'name' => (string)$value->name,
                'price' => (float)$value->price,
                'qty' => max(1, (int)$value->qty),
            ])->values()->all(),
        ])->values()->all();
    }

    /**
     * @param  list<array<string, mixed>>  $selectedOptions
     * @return list<array{id: int, name: string, values: list<array{id: int, name: string, price: float, qty: int}>}>
     */
    protected function cartOptionsForMenu(Menu $menu, array $selectedOptions): array
    {
        /** @var Collection<int, MenuItemOption> $menuOptions */
        $menuOptions = $menu->menu_options->keyBy('menu_option_id');
        $result = [];

        foreach ($selectedOptions as $selected) {
            if (!is_array($selected)) {
                continue;
            }

            $optionId = (int)($selected['id'] ?? 0);
            $menuOption = $menuOptions->get($optionId);
            if (!$menuOption instanceof MenuItemOption) {
                continue;
            }

            $values = [];
            foreach ((array)($selected['values'] ?? []) as $selectedValue) {
                if (!is_array($selectedValue)) {
                    continue;
                }

                $valueId = (int)($selectedValue['id'] ?? 0);
                /** @var MenuItemOptionValue|null $optionValue */
                $optionValue = $menuOption->menu_option_values->firstWhere('menu_option_value_id', $valueId);
                if (!$optionValue instanceof MenuItemOptionValue) {
                    continue;
                }

                $values[] = [
                    'id' => (int)$optionValue->menu_option_value_id,
                    'name' => (string)$optionValue->name,
                    'price' => (float)$optionValue->price,
                    'qty' => max(1, (int)($selectedValue['qty'] ?? 1)),
                ];
            }

            if ($values === []) {
                continue;
            }

            $result[] = [
                'id' => (int)$menuOption->menu_option_id,
                'name' => (string)$menuOption->option_name,
                'values' => $values,
            ];
        }

        return $result;
    }

    /**
     * Order-level totals only (cart conditions, subtotal, total).
     * Item line amounts are returned separately as order_menus.
     *
     * @return array<string, array{code: string, title: string, value: float, priority: int, is_summable: bool}>
     */
    protected function buildOrderTotals(Cart $cart): array
    {
        $totals = $cart->conditions()->map(fn(CartCondition $condition): array => [
            'code' => $condition->name,
            'title' => $condition->getLabel(),
            'value' => is_numeric($value = $condition->getValue()) ? (float)$value : 0.0,
            'priority' => $condition->getPriority() ?: 1,
            'is_summable' => !$condition->isInclusive(),
        ])->all();

        $totals['subtotal'] = [
            'code' => 'subtotal',
            'title' => lang('igniter.cart::default.text_sub_total'),
            'value' => (float)$cart->subtotal(),
            'priority' => 0,
            'is_summable' => false,
        ];

        $totals['total'] = [
            'code' => 'total',
            'title' => lang('igniter.cart::default.text_order_total'),
            'value' => (float)max(0, $cart->total()),
            'priority' => 999,
            'is_summable' => false,
        ];

        return $totals;
    }
}
