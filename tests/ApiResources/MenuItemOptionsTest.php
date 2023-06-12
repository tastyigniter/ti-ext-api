<?php

namespace Igniter\Api\Tests\ApiResources;

use Igniter\Cart\Models\MenuItemOption;
use Igniter\Cart\Models\MenuOption;
use Igniter\User\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;

it('returns all menu item options', function () {
    Sanctum::actingAs(User::factory()->create(), ['menu_item_options:*']);

    $this
        ->get(route('igniter.api.menu_item_options.index'))
        ->assertOk()
        ->assertJsonPath('data.0.attributes.option_name', MenuItemOption::first()->option_name);
});

it('shows a menu item option', function () {
    Sanctum::actingAs(User::factory()->create(), ['menu_item_options:*']);
    $menuItemOption = MenuItemOption::first();

    $this
        ->get(route('igniter.api.menu_item_options.show', [$menuItemOption->getKey()]))
        ->assertOk()
        ->assertJson(fn (AssertableJson $json) => $json
            ->has('data.attributes.option')
            ->has('data.attributes', fn (AssertableJson $json) => $json
                ->where('option_id', $menuItemOption->option->getKey())
                ->etc()
            )->etc()
        );
});

it('creates a menu item option', function () {
    Sanctum::actingAs(User::factory()->create(), ['menu_item_options:*']);
    $menuOption = MenuOption::first();

    $this
        ->post(route('igniter.api.menu_item_options.store'), [
            'option_id' => $menuOption->getKey(),
        ])
        ->assertCreated()
        ->assertJson(fn (AssertableJson $json) => $json
            ->has('data.attributes', fn (AssertableJson $json) => $json
                ->where('option_id', $menuOption->getKey())
                ->etc()
            ));
});

it('updates a menu item option', function () {
    Sanctum::actingAs(User::factory()->create(), ['menu_item_options:*']);
    $menuItemOption = MenuItemOption::first();

    $this
        ->put(route('igniter.api.menu_item_options.update', [$menuItemOption->getKey()]), [
            'option_id' => $menuItemOption->option->getKey(),
        ])
        ->assertOk()
        ->assertJson(fn (AssertableJson $json) => $json
            ->has('data.attributes', fn (AssertableJson $json) => $json
                ->where('option_id', $menuItemOption->option->getKey())
                ->etc()
            )->etc());
});

it('deletes a menu item option', function () {
    Sanctum::actingAs(User::factory()->create(), ['menu_item_options:*']);
    $menuItemOption = MenuItemOption::first();

    $this
        ->delete(route('igniter.api.menu_item_options.destroy', [$menuItemOption->getKey()]))
        ->assertStatus(204);
});