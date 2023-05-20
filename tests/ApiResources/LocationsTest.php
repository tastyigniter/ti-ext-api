<?php

namespace Igniter\Api\Tests\ApiResources;

use Igniter\Admin\Models\Location;
use Igniter\Admin\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;

it('returns all locations', function () {
    Sanctum::actingAs(User::factory()->create(), ['locations:*']);
    Location::factory()->count(4)->create();

    $this
        ->get(route('igniter.api.locations.index'))
        ->assertOk()
        ->assertJsonPath('data.0.attributes', Location::first()->toArray())
        ->assertJsonCount(5, 'data');
});

it('shows a location', function () {
    Sanctum::actingAs(User::factory()->create(), ['locations:*']);
    $location = Location::first();

    $this
        ->get(route('igniter.api.locations.show', [$location->getKey()]))
        ->assertOk()
        ->assertJson(fn (AssertableJson $json) => $json
            ->has('data.attributes', fn (AssertableJson $json) => $json
                ->where('location_name', $location->location_name)
                ->where('location_email', $location->location_email)
                ->where('location_address_1', $location->location_address_1)
                ->etc()
            ));
});

it('creates a location', function () {
    Sanctum::actingAs(User::factory()->create(), ['locations:*']);

    $this
        ->post(route('igniter.api.locations.store'), [
            'location_name' => 'Test Location',
            'location_email' => 'test@location.tld',
            'location_address_1' => '123 Test Address',
            'options' => [
                'auto_lat_lng' => '0',
            ],
        ])
        ->assertCreated()
        ->assertJson(fn (AssertableJson $json) => $json
            ->has('data.attributes', fn (AssertableJson $json) => $json
                ->where('location_name', 'Test Location')
                ->where('location_email', 'test@location.tld')
                ->where('location_address_1', '123 Test Address')
                ->etc()
            ));
});

it('updates a location', function () {
    Sanctum::actingAs(User::factory()->create(), ['locations:*']);
    $location = Location::factory()->create();

    $this
        ->put(route('igniter.api.locations.update', [$location->getKey()]), [
            'location_name' => 'Test Location',
            'location_email' => 'test@location.tld',
            'location_address_1' => '123 Test Address',
            'options' => [
                'auto_lat_lng' => '0',
            ],
        ])
        ->assertOk()
        ->assertJson(fn (AssertableJson $json) => $json
            ->has('data.attributes', fn (AssertableJson $json) => $json
                ->where('location_name', 'Test Location')
                ->where('location_email', 'test@location.tld')
                ->where('location_address_1', '123 Test Address')
                ->etc()
            ));
});

it('deletes a location', function () {
    Sanctum::actingAs(User::factory()->create(), ['locations:*']);
    $location = Location::factory()->create();

    $this
        ->delete(route('igniter.api.locations.destroy', [$location->getKey()]))
        ->assertStatus(204);
});