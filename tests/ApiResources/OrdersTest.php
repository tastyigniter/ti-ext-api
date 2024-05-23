<?php

namespace Igniter\Api\Tests\ApiResources;

use Igniter\Admin\Models\Status;
use Igniter\Cart\Models\Order;
use Igniter\Local\Models\Location;
use Igniter\User\Models\User;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\Sanctum;

it('returns all orders', function() {
    Sanctum::actingAs(User::factory()->create(), ['orders:*']);
    Order::factory()->count(3)->create();

    $this
        ->get(route('igniter.api.orders.index'))
        ->assertOk()
        ->assertJsonPath('data.0.attributes.email', Order::first()->email);
});

it('shows an order', function() {
    Sanctum::actingAs(User::factory()->create(), ['orders:*']);
    $order = Order::factory()->create();

    $this
        ->get(route('igniter.api.orders.show', [$order->getKey()]))
        ->assertOk()
        ->assertJsonPath('data.attributes.email', $order->email);
});

it('creates an order', function() {
    Mail::fake();

    Sanctum::actingAs(User::factory()->create(), ['orders:*']);

    $this
        ->post(route('igniter.api.orders.store'), [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test-user@domain.tld',
            'telephone' => '1234567890',
            'comment' => '123 Test St',
            'payment' => 'cod',
            'status_id' => Status::isForOrder()->first()->getKey(),
            'order_type' => Location::DELIVERY,
            'address' => [
                'address_1' => '123 Test St',
                'city' => 'Test City',
                'postcode' => '12345',
                'state' => 'Test State',
                'country_id' => '123',
            ],
            'order_menus' => [],
            'order_totals' => [],
        ])
        ->assertCreated()
        ->assertJsonPath('data.attributes.email', 'test-user@domain.tld');
});

it('updates an order', function() {
    Mail::fake();

    Sanctum::actingAs(User::factory()->create(), ['orders:*']);
    $order = Order::factory()->create();

    expect($order->order_type)->toBe(Location::DELIVERY);

    $this
        ->put(route('igniter.api.orders.update', [$order->getKey()]), [
            'order_type' => Location::COLLECTION,
        ])
        ->assertOk()
        ->assertJsonPath('data.attributes.order_type', Location::COLLECTION);
});

it('deletes an order', function() {
    Sanctum::actingAs(User::factory()->create(), ['orders:*']);
    $order = Order::factory()->create();

    $this
        ->delete(route('igniter.api.orders.destroy', [$order->getKey()]))
        ->assertStatus(204);

    expect(Order::find($order->getKey()))->toBeNull();
});
