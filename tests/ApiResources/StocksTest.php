<?php

declare(strict_types=1);

namespace Igniter\Api\Tests\ApiResources;

use Igniter\Api\ApiResources\Stocks;
use Igniter\Cart\Models\Menu;
use Igniter\Cart\Models\MenuItemOptionValue;
use Igniter\Cart\Models\Stock;
use Igniter\Local\Models\Location;
use Igniter\User\Models\User;
use Laravel\Sanctum\Sanctum;

it('returns all stocks', function(): void {
    Sanctum::actingAs(User::factory()->create(), ['stocks:*']);
    $menu = Menu::factory()->create();
    $stock = Stock::factory()->create([
        'stockable_id' => $menu->getKey(),
        'stockable_type' => $menu->getMorphClass(),
    ]);
    $stock->quantity = 4;
    $stock->saveQuietly();

    $this->get(route('igniter.api.stocks.index'))
        ->assertOk()
        ->assertJsonPath('data.0.attributes.stockable_name', $menu->menu_name)
        ->assertJsonPath('data.0.attributes.quantity', 4);
});

it('filters stocks by location_id', function(): void {
    Sanctum::actingAs(User::factory()->create(), ['stocks:*']);
    $menu = Menu::factory()->create();
    $locationA = Location::factory()->create();
    $locationB = Location::factory()->create();
    $stockA = Stock::factory()->create([
        'location_id' => $locationA->getKey(),
        'stockable_id' => $menu->getKey(),
        'stockable_type' => $menu->getMorphClass(),
    ]);
    Stock::factory()->create([
        'location_id' => $locationB->getKey(),
        'stockable_id' => $menu->getKey(),
        'stockable_type' => $menu->getMorphClass(),
    ]);

    $this->get(route('igniter.api.stocks.index', [
        'location_id' => $locationA->getKey(),
    ]))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', (string)$stockA->getKey());
});

it('filters stocks by search against the menu name', function(): void {
    Sanctum::actingAs(User::factory()->create(), ['stocks:*']);
    $match = Menu::factory()->create(['menu_name' => 'Spicy Wings']);
    $other = Menu::factory()->create(['menu_name' => 'Plain Rice']);
    $matchStock = Stock::factory()->create([
        'stockable_id' => $match->getKey(),
        'stockable_type' => $match->getMorphClass(),
    ]);
    Stock::factory()->create([
        'stockable_id' => $other->getKey(),
        'stockable_type' => $other->getMorphClass(),
    ]);

    $this->get(route('igniter.api.stocks.index', [
        'search' => 'Wings',
    ]))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', (string)$matchStock->getKey());
});

it('shows a stock', function(): void {
    Sanctum::actingAs(User::factory()->create(), ['stocks:*']);
    $menu = Menu::factory()->create();
    $stock = Stock::factory()->create([
        'stockable_id' => $menu->getKey(),
        'stockable_type' => $menu->getMorphClass(),
    ]);

    $this->get(route('igniter.api.stocks.show', [$stock->getKey()]))
        ->assertOk()
        ->assertJsonPath('data.id', (string)$stock->getKey())
        ->assertJsonPath('data.attributes.stockable_type', 'menus');
});

it('creates a stock and sets the starting quantity', function(): void {
    Sanctum::actingAs(User::factory()->create(), ['stocks:*']);
    $menu = Menu::factory()->create();
    $location = Location::factory()->create();

    $this->post(route('igniter.api.stocks.store'), [
        'location_id' => $location->getKey(),
        'stockable_id' => $menu->getKey(),
        'stockable_type' => 'menus',
        'is_tracked' => true,
        'low_stock_alert' => true,
        'low_stock_threshold' => 2,
        'quantity' => 6,
    ])
        ->assertCreated()
        ->assertJsonPath('data.attributes.quantity', 6)
        ->assertJsonPath('data.attributes.stockable_name', $menu->menu_name);

    expect(Stock::query()->where('stockable_id', $menu->getKey())->value('quantity'))->toBe(6);
});

it('rejects a duplicate stock row', function(): void {
    Sanctum::actingAs(User::factory()->create(), ['stocks:*']);
    $menu = Menu::factory()->create();
    $stock = Stock::factory()->create([
        'stockable_id' => $menu->getKey(),
        'stockable_type' => $menu->getMorphClass(),
    ]);

    $this->post(route('igniter.api.stocks.store'), [
        'location_id' => $stock->location_id,
        'stockable_id' => $menu->getKey(),
        'stockable_type' => 'menus',
        'is_tracked' => true,
    ])->assertUnprocessable();
});

it('updates a stock quantity through a stock action', function(): void {
    Sanctum::actingAs($user = User::factory()->create(), ['stocks:*']);
    $menu = Menu::factory()->create();
    $stock = Stock::factory()->create([
        'stockable_id' => $menu->getKey(),
        'stockable_type' => $menu->getMorphClass(),
        'is_tracked' => true,
    ]);
    $stock->quantity = 3;
    $stock->saveQuietly();

    $this->put(route('igniter.api.stocks.update', [$stock->getKey()]), [
        'stock_action' => [
            'state' => Stock::STATE_RESTOCK,
            'quantity' => 2,
        ],
    ])
        ->assertOk()
        ->assertJsonPath('data.attributes.quantity', 5);

    expect($stock->refresh()->history()->latest('id')->first())
        ->user_id->toBe($user->getKey())
        ->state->toBe(Stock::STATE_RESTOCK);
});

it('applies an indefinite out of stock override', function(): void {
    Sanctum::actingAs(User::factory()->create(), ['stocks:*']);
    $menu = Menu::factory()->create();
    $stock = Stock::factory()->create([
        'stockable_id' => $menu->getKey(),
        'stockable_type' => $menu->getMorphClass(),
        'is_tracked' => true,
    ]);

    $this->put(route('igniter.api.stocks.update', [$stock->getKey()]), [
        'out_of_stock_type' => Stock::OOS_INDEFINITELY,
    ])
        ->assertOk()
        ->assertJsonPath('data.attributes.out_of_stock_type', Stock::OOS_INDEFINITELY);
});

it('clears an out of stock override', function(): void {
    Sanctum::actingAs(User::factory()->create(), ['stocks:*']);
    $menu = Menu::factory()->create();
    $stock = Stock::factory()->create([
        'stockable_id' => $menu->getKey(),
        'stockable_type' => $menu->getMorphClass(),
        'is_tracked' => true,
    ]);
    $stock->applyOutOfStockOverride(Stock::OOS_INDEFINITELY);

    $this->put(route('igniter.api.stocks.update', [$stock->getKey()]), [
        'out_of_stock_type' => null,
    ])->assertOk();

    expect($stock->refresh()->out_of_stock_type)->toBeNull();
});

it('ignores restAfterSave for non-stock models', function(): void {
    $controller = new Stocks;

    expect(fn() => $controller->restAfterSave(new Menu))->not->toThrow(Throwable::class);
});

it('creates a stock for a menu option value', function(): void {
    Sanctum::actingAs(User::factory()->create(), ['stocks:*']);
    $optionValue = MenuItemOptionValue::factory()->create();
    $location = Location::factory()->create();

    $this->post(route('igniter.api.stocks.store'), [
        'location_id' => $location->getKey(),
        'stockable_id' => $optionValue->getKey(),
        'stockable_type' => 'menu_option_values',
        'is_tracked' => true,
        'quantity' => 3,
    ])
        ->assertCreated()
        ->assertJsonPath('data.attributes.stockable_type', 'menu_option_values');
});

it('rejects an invalid stockable for menu option values', function(): void {
    Sanctum::actingAs(User::factory()->create(), ['stocks:*']);
    $location = Location::factory()->create();

    $this->post(route('igniter.api.stocks.store'), [
        'location_id' => $location->getKey(),
        'stockable_id' => 999999,
        'stockable_type' => 'menu_option_values',
        'is_tracked' => true,
    ])->assertUnprocessable();
});
