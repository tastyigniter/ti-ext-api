<?php

namespace Igniter\Api\Tests\ApiResources;

use Igniter\Cart\Models\Menu;
use Igniter\User\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;

it('returns all menu items', function () {
    Sanctum::actingAs(User::factory()->create(), ['menus:*']);

    $this
        ->get(route('igniter.api.menus.index'))
        ->assertOk()
        ->assertJsonPath('data.0.attributes.menu_name', Menu::first()->menu_name);
});

it('shows a menu item', function () {
    Sanctum::actingAs(User::factory()->create(), ['menus:*']);
    $menu = Menu::first();

    $this
        ->get(route('igniter.api.menus.show', [$menu->getKey()]))
        ->assertOk()
        ->assertJson(fn (AssertableJson $json) => $json
            ->has('data.attributes', fn (AssertableJson $json) => $json
                ->where('menu_name', $menu->menu_name)
                ->where('menu_price', $menu->menu_price)
                ->etc()
            )->etc()
        );
});

it('creates a menu item', function () {
    Sanctum::actingAs(User::factory()->create(), ['menus:*']);
    $menu = Menu::first();

    $this
        ->post(route('igniter.api.menus.store'), [
            'menu_name' => 'Test menu item',
            'menu_price' => 99.999,
        ])
        ->assertCreated()
        ->assertJson(fn (AssertableJson $json) => $json
            ->has('data.attributes', fn (AssertableJson $json) => $json
                ->where('menu_name', 'Test menu item')
                ->where('menu_price', 99.999)
                ->etc()
            ));
});

it('updates a menu item', function () {
    Sanctum::actingAs(User::factory()->create(), ['menus:*']);
    $menu = Menu::first();

    $this
        ->put(route('igniter.api.menus.update', [$menu->getKey()]), [
            'menu_name' => 'Test menu item',
            'menu_price' => 99.999,
        ])
        ->assertOk()
        ->assertJson(fn (AssertableJson $json) => $json
            ->has('data.attributes', fn (AssertableJson $json) => $json
                ->where('menu_name', 'Test menu item')
                ->where('menu_price', 99.999)
                ->etc()
            )->etc());
});

it('deletes a menu item', function () {
    Sanctum::actingAs(User::factory()->create(), ['menus:*']);
    $menu = Menu::first();

    $this
        ->delete(route('igniter.api.menus.destroy', [$menu->getKey()]))
        ->assertStatus(204);
});