<?php

declare(strict_types=1);

namespace Igniter\Api\Tests\Classes;

use Igniter\Api\Classes\OrderTotalsCalculator;
use Igniter\Cart\Cart;
use Igniter\Cart\CartItem;
use Igniter\Cart\CartItemOptions;
use Igniter\Cart\Classes\CartConditionManager;
use Igniter\Cart\Models\CartSettings;
use Igniter\Cart\Models\Menu;
use Igniter\Cart\Models\MenuOption;
use Igniter\Local\Models\Location;
use ReflectionMethod;

it('skips disabled and empty cart conditions', function(): void {
    $location = Location::factory()->create();
    $menu = Menu::factory()->create(['menu_price' => 5]);

    $realManager = resolve(CartConditionManager::class);
    $definitions = [
        ['name' => 'broken', 'status' => true, 'className' => ''],
    ];
    foreach ($realManager->listRegisteredConditions() as $condition) {
        $definitions[] = array_merge($condition, ['status' => false]);
    }

    $manager = mock(CartConditionManager::class);
    $manager->shouldReceive('listRegisteredConditions')->andReturn($definitions);
    $manager->shouldReceive('makeCondition')->andReturnUsing(
        fn(...$args) => $realManager->makeCondition(...$args),
    );
    app()->instance(CartConditionManager::class, $manager);

    CartSettings::clearInternalCache();
    CartSettings::set('conditions', collect($definitions)->mapWithKeys(
        fn(array $condition): array => [$condition['name'] => ['status' => $condition['status']]],
    )->all());

    $result = resolve(OrderTotalsCalculator::class)->calculate(
        (int)$location->getKey(),
        Location::COLLECTION,
        [['id' => $menu->getKey(), 'qty' => 1]],
    );

    expect($result['order_menus'])->toHaveCount(1)
        ->and(collect($result['order_totals'])->pluck('code')->all())->toContain('subtotal', 'total');
});

it('includes selected menu options in calculated totals', function(): void {
    $location = Location::factory()->create();
    $menu = Menu::factory()->create(['menu_price' => 10]);
    $option = MenuOption::factory()->create(['display_type' => 'radio']);
    $menuOption = $menu->menu_options()->create(['option_id' => $option->getKey()]);
    $menuOptionValue = $menuOption->menu_option_values()->create([
        'option_value_id' => 1,
        'price' => 2,
    ]);

    $result = resolve(OrderTotalsCalculator::class)->calculate(
        (int)$location->getKey(),
        Location::COLLECTION,
        [[
            'id' => $menu->getKey(),
            'qty' => 1,
            'line_id' => 'line-opt',
            'options' => [
                'skip-me',
                ['id' => 999999, 'values' => [['id' => 1]]],
                [
                    'id' => $menuOption->getKey(),
                    'values' => [
                        'skip',
                        ['id' => 999999],
                        ['id' => $menuOptionValue->getKey(), 'qty' => 2],
                    ],
                ],
                [
                    'id' => $menuOption->getKey(),
                    'values' => [],
                ],
            ],
        ]],
    );

    expect($result['order_menus'][0]['options'])->toHaveCount(1)
        ->and($result['order_menus'][0]['options'][0]['values'][0]['id'])->toBe($menuOptionValue->getKey())
        ->and($result['order_menus'][0]['options'][0]['values'][0]['qty'])->toBe(2);
});

it('uses zero unit prices when cart item quantity is zero', function(): void {
    $location = Location::factory()->create();
    $menu = Menu::factory()->create(['menu_price' => 10]);

    $cartItem = mock(CartItem::class)->makePartial();
    $cartItem->qty = 0;
    $cartItem->name = 'Zero';
    $cartItem->options = new CartItemOptions([]);
    $cartItem->shouldReceive('hasConditions')->andReturn(0);

    $cart = mock(Cart::class);
    $cart->shouldReceive('add')->once()->andReturn($cartItem);

    $result = (new ReflectionMethod(OrderTotalsCalculator::class, 'addMenuToCart'))
        ->invoke(new OrderTotalsCalculator, $cart, ['id' => $menu->getKey(), 'qty' => 1], $location);

    expect($result['price'])->toBe(0.0)
        ->and($result['subtotal'])->toBe(0.0)
        ->and($result['subtotalWithoutConditions'])->toBe(0.0);
});
