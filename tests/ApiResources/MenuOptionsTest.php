<?php

namespace Igniter\Api\Tests\ApiResources;

use Igniter\Admin\Models\MenuOption;
use Igniter\Admin\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;

it('returns all menu options', function () {
    Sanctum::actingAs(User::factory()->create(), ['menu_options:*']);

    $this
        ->get(route('igniter.api.menu_options.index'))
        ->assertOk()
        ->assertJsonPath('data.0.attributes.option_name', MenuOption::first()->option_name);
});

it('shows a menu option', function () {
    Sanctum::actingAs(User::factory()->create(), ['menu_options:*']);
    $menuOption = MenuOption::first();

    $this
        ->get(route('igniter.api.menu_options.show', [$menuOption->getKey()]))
        ->assertOk()
        ->assertJson(fn(AssertableJson $json) => $json
            ->has('data.attributes', fn(AssertableJson $json) => $json
                ->where('option_name', $menuOption->option_name)
                ->where('display_type', $menuOption->display_type)
                ->etc()
            )->etc()
        );
});

it('creates a menu option', function () {
    Sanctum::actingAs(User::factory()->create(), ['menu_options:*']);
    $menuOption = MenuOption::first();

    $this
        ->post(route('igniter.api.menu_options.store'), [
            'option_name' => 'Test menu option',
            'display_type' => 'radio',
        ])
        ->assertCreated()
        ->assertJson(fn(AssertableJson $json) => $json
            ->has('data.attributes', fn(AssertableJson $json) => $json
                ->where('option_name', 'Test menu option')
                ->where('display_type', 'radio')
                ->etc()
            ));
});

it('updates a menu option', function () {
    Sanctum::actingAs(User::factory()->create(), ['menu_options:*']);
    $menuOption = MenuOption::first();

    $this
        ->put(route('igniter.api.menu_options.update', [$menuOption->getKey()]), [
            'option_name' => 'Test menu option',
            'display_type' => 'radio',
        ])
        ->assertOk()
        ->assertJson(fn(AssertableJson $json) => $json
            ->has('data.attributes', fn(AssertableJson $json) => $json
                ->where('option_name', 'Test menu option')
                ->where('display_type', 'radio')
                ->etc()
            )->etc());
});

it('deletes a menu option', function () {
    Sanctum::actingAs(User::factory()->create(), ['menu_options:*']);
    $menuOption = MenuOption::first();

    $this
        ->delete(route('igniter.api.menu_options.destroy', [$menuOption->getKey()]))
        ->assertStatus(204);
});