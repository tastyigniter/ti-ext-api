<?php

namespace Igniter\Api\Tests\ApiResources;

use Igniter\Cart\Models\Category;
use Igniter\User\Models\User;
use Laravel\Sanctum\Sanctum;

it('returns all categories', function() {
    Sanctum::actingAs(User::factory()->create(), ['categories:*']);

    $this
        ->get(route('igniter.api.categories.index'))
        ->assertOk()
        ->assertJsonPath('data.0.attributes.name', Category::first()->name);
});

it('shows a category', function() {
    Sanctum::actingAs(User::factory()->create(), ['categories:*']);
    $category = Category::first();

    $this
        ->get(route('igniter.api.categories.show', [$category->getKey()]))
        ->assertOk()
        ->assertJsonPath('data.attributes.name', $category->name);
});

it('shows a category with media relationship', function() {
    Sanctum::actingAs(User::factory()->create(), ['categories:*']);
    $category = Category::first();
    $category->media()->create(['file_name' => 'test.jpg', 'tag' => 'thumb']);

    $this
        ->get(route('igniter.api.categories.show', [$category->getKey()]).'?'.http_build_query([
                'include' => 'media',
            ]))
        ->assertOk()
        ->assertJsonPath('data.relationships.media.data.type', 'media')
        ->assertJsonPath('included.0.attributes.file_name', 'test.jpg');
});

it('shows a category with menus relationship', function() {
    Sanctum::actingAs(User::factory()->create(), ['categories:*']);
    $category = Category::first();
    $category->menus()->create(['menu_name' => 'Test Menu']);

    $this
        ->get(route('igniter.api.categories.show', [$category->getKey()]).'?'.http_build_query([
                'include' => 'menus',
            ]))
        ->assertOk()
        ->assertJsonPath('data.relationships.menus.data.0.type', 'menus')
        ->assertJsonPath('included.0.attributes.menu_name', 'Test Menu');
});

it('shows a category with locations relationship', function() {
    Sanctum::actingAs(User::factory()->create(), ['categories:*']);
    $category = Category::first();
    $category->locations()->create(['location_name' => 'Test Location']);

    $this
        ->get(route('igniter.api.categories.show', [$category->getKey()]).'?'.http_build_query([
                'include' => 'locations',
            ]))
        ->assertOk()
        ->assertJsonPath('data.relationships.locations.data.0.type', 'locations')
        ->assertJsonPath('included.0.attributes.location_name', 'Test Location');
});

it('creates a category', function() {
    Sanctum::actingAs(User::factory()->create(), ['categories:*']);

    $this
        ->post(route('igniter.api.categories.store'), [
            'name' => 'Test Category',
        ])
        ->assertCreated()
        ->assertJsonPath('data.attributes.name', 'Test Category');
});

it('updates a category', function() {
    Sanctum::actingAs(User::factory()->create(), ['categories:*']);
    $category = Category::first();

    $this
        ->put(route('igniter.api.categories.update', [$category->getKey()]), [
            'name' => 'Test Category',
        ])
        ->assertOk()
        ->assertJsonPath('data.attributes.name', 'Test Category');
});

it('deletes a category', function() {
    Sanctum::actingAs(User::factory()->create(), ['categories:*']);
    $category = Category::first();

    $this
        ->delete(route('igniter.api.categories.destroy', [$category->getKey()]))
        ->assertStatus(204);
});
