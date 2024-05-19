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
        ->assertJsonPath('data.0.attributes', Category::first()->toArray());
});

it('shows a category', function() {
    Sanctum::actingAs(User::factory()->create(), ['categories:*']);
    $category = Category::first();

    $this
        ->get(route('igniter.api.categories.show', [$category->getKey()]))
        ->assertOk()
        ->assertJsonPath('data.attributes', $category->toArray());
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