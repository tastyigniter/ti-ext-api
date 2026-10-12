<?php

declare(strict_types=1);

namespace Igniter\Api\Tests\Classes;

use Igniter\Api\Classes\OrderTotalsCalculator;
use Igniter\Cart\Cart;
use Igniter\Cart\CartCondition;
use Igniter\Cart\CartItem;
use Igniter\Cart\CartItemOptions;
use Igniter\Cart\Classes\CartConditionManager;
use Igniter\Cart\Models\CartSettings;
use Igniter\Cart\Models\Menu;
use Igniter\Cart\Models\MenuOption;
use Igniter\Coupons\Models\Coupon;
use Igniter\Flame\Exception\ApplicationException;
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

it('applies a coupon and a custom tip to calculated totals', function(): void {
    CartSettings::set(['enable_tipping' => true, 'tip_value_type' => 'F']);
    $location = Location::factory()->create();
    $menu = Menu::factory()->create(['menu_price' => 20]);
    Coupon::factory()->create([
        'code' => 'SAVE2',
        'type' => 'F',
        'discount' => 2,
        'min_total' => 0,
        'status' => 1,
        'validity' => 'forever',
    ]);

    $result = resolve(OrderTotalsCalculator::class)->calculate(
        (int)$location->getKey(),
        Location::COLLECTION,
        [['id' => $menu->getKey(), 'qty' => 1]],
        'SAVE2',
        1.5,
        true,
    );

    $totals = collect($result['order_totals'])->keyBy('code');
    expect($totals->has('coupon'))->toBeTrue()
        ->and($totals->has('tip'))->toBeTrue()
        ->and((float)$totals->get('tip')['value'])->toBe(1.5)
        ->and($result['order_total'])->toBeLessThan(20.0);
});

it('rejects an unknown coupon', function(): void {
    $location = Location::factory()->create();
    $menu = Menu::factory()->create(['menu_price' => 10]);

    expect(fn() => resolve(OrderTotalsCalculator::class)->calculate(
        (int)$location->getKey(),
        Location::COLLECTION,
        [['id' => $menu->getKey(), 'qty' => 1]],
        'NOT-A-COUPON',
    ))->toThrow(ApplicationException::class);
});

it('creates a missing cart condition from the registry', function(): void {
    $condition = mock(CartCondition::class);
    $cart = mock(Cart::class);
    $cart->shouldReceive('getCondition')->once()->with('coupon')->andReturn(null);
    $cart->shouldReceive('loadCondition')->once()->with($condition);

    $manager = mock(CartConditionManager::class);
    $manager->shouldReceive('listRegisteredConditions')->once()->andReturn([
        'Ignored\\Class' => ['label' => 'missing name'],
        'Other\\Class' => ['name' => 'tax'],
        \Igniter\Coupons\CartConditions\Coupon::class => ['name' => 'coupon', 'label' => 'Coupon'],
    ]);
    $manager->shouldReceive('makeCondition')
        ->once()
        ->with(\Igniter\Coupons\CartConditions\Coupon::class, ['name' => 'coupon', 'label' => 'Coupon'])
        ->andReturn($condition);
    app()->instance(CartConditionManager::class, $manager);

    expect(callProtectedMethod(new OrderTotalsCalculator, 'cartCondition', [$cart, 'coupon']))
        ->toBe($condition);
});

it('rejects a coupon when the coupons extension is unavailable', function(): void {
    $calculator = new class extends OrderTotalsCalculator
    {
        protected function couponClassExists(): bool
        {
            return false;
        }
    };

    expect(fn(): mixed => callProtectedMethod($calculator, 'applyCoupon', [
        mock(Cart::class),
        'SAVE2',
        Location::COLLECTION,
        Location::factory()->create(),
    ]))->toThrow(ApplicationException::class);

    expect(callProtectedMethod(new OrderTotalsCalculator, 'couponClassExists'))->toBeTrue();
});

it('rejects a coupon when no coupon condition is registered', function(): void {
    $location = Location::factory()->create();
    $menu = Menu::factory()->create(['menu_price' => 10]);
    Coupon::factory()->create([
        'code' => 'ORPHAN',
        'type' => 'F',
        'discount' => 1,
        'min_total' => 0,
        'status' => 1,
        'validity' => 'forever',
    ]);

    $realManager = resolve(CartConditionManager::class);
    $registered = collect($realManager->listRegisteredConditions() ?? [])
        ->reject(fn(array $definition): bool => ($definition['name'] ?? '') === 'coupon')
        ->all();

    $manager = mock(CartConditionManager::class);
    $manager->shouldReceive('listRegisteredConditions')->andReturn($registered);
    $manager->shouldReceive('makeCondition')->andReturnUsing(
        fn(...$args) => $realManager->makeCondition(...$args),
    );
    app()->instance(CartConditionManager::class, $manager);
    CartSettings::clearInternalCache();

    expect(fn() => resolve(OrderTotalsCalculator::class)->calculate(
        (int)$location->getKey(),
        Location::COLLECTION,
        [['id' => $menu->getKey(), 'qty' => 1]],
        'ORPHAN',
    ))->toThrow(ApplicationException::class);
});

it('ignores a tip when the tip condition cannot be resolved', function(): void {
    CartSettings::set('enable_tipping', true);

    $cart = mock(Cart::class);
    $cart->shouldReceive('getCondition')->with('tip')->andReturn(null);
    $cart->shouldNotReceive('loadCondition');

    $manager = mock(CartConditionManager::class);
    $manager->shouldReceive('listRegisteredConditions')->andReturn(null, [
        'Ignored\\Class' => ['label' => 'missing name'],
        'Other\\Class' => ['name' => 'tax'],
    ]);
    app()->instance(CartConditionManager::class, $manager);

    $calculator = new OrderTotalsCalculator;
    callProtectedMethod($calculator, 'applyTip', [$cart, 3.0, true]);
    callProtectedMethod($calculator, 'applyTip', [$cart, 3.0, false]);
});
