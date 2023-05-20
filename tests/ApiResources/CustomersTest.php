<?php

namespace Igniter\Api\Tests\ApiResources;

use Igniter\Admin\Models\User;
use Igniter\Main\Models\Customer;
use Laravel\Sanctum\Sanctum;

it('can not update customer aware column', function () {
    Sanctum::actingAs($customer = Customer::factory()->create(), ['customers:*']);

    $this
        ->put(route('igniter.api.customers.update', [$customer->getKey()]), [
            'customer_id' => 9999,
            'first_name' => 'Test',
            'last_name' => 'Customer',
            'email' => 'test@example.tld',
            'customer_group_id' => 1,
            'status' => false,
        ])
        ->assertOk()
        ->assertJsonPath('data.attributes.full_name', 'Test Customer')
        ->assertJsonMissing(['customer_id' => 9999]);
});

it('returns only authenticated customer', function () {
    Customer::factory()->count(5)->create();
    Sanctum::actingAs($customer = Customer::factory()->create(), ['customers:*']);

    $this
        ->get(route('igniter.api.customers.index'))
        ->assertOk()
        ->assertJsonPath('data.0.attributes.full_name', $customer->full_name)
        ->assertJsonCount(1, 'data');
});

it('can not show unauthenticated customer', function () {
    $anotherCustomer = Customer::factory()->create();
    Sanctum::actingAs(Customer::factory()->create(), ['customers:*']);

    $this
        ->get(route('igniter.api.customers.show', [$anotherCustomer->getKey()]))
        ->assertStatus(404);
});

it('returns all customers', function () {
    Sanctum::actingAs(User::factory()->create(), ['customers:*']);
    Customer::factory()->count(5)->create();

    $this
        ->get(route('igniter.api.customers.index'))
        ->assertOk()
        ->assertJsonPath('data.0.attributes', Customer::first()->toArray())
        ->assertJsonCount(5, 'data');
});

it('shows a customer', function () {
    Sanctum::actingAs(User::factory()->create(), ['customers:*']);
    $customer = Customer::factory()->create();

    $this
        ->get(route('igniter.api.customers.show', [$customer->getKey()]))
        ->assertOk()
        ->assertJsonPath('data.attributes.full_name', $customer->full_name);
});

it('creates a customer', function () {
    Sanctum::actingAs(User::factory()->create(), ['customers:*']);

    $this
        ->post(route('igniter.api.customers.store'), [
            'first_name' => 'Test',
            'last_name' => 'Customer',
            'email' => 'test@example.tld',
            'customer_group_id' => 1,
            'status' => false,
        ])
        ->assertCreated()
        ->assertJsonPath('data.attributes.full_name', 'Test Customer');
});

it('updates a customer', function () {
    Sanctum::actingAs(User::factory()->create(), ['customers:*']);
    $customer = Customer::factory()->create();

    $this
        ->put(route('igniter.api.customers.update', [$customer->getKey()]), [
            'first_name' => 'Test',
            'last_name' => 'Customer',
            'email' => 'test@example.tld',
            'customer_group_id' => 1,
            'status' => false,
        ])
        ->assertOk()
        ->assertJsonPath('data.attributes.full_name', 'Test Customer');
});

it('deletes a customer', function () {
    Sanctum::actingAs(User::factory()->create(), ['customers:*']);
    $customer = Customer::factory()->create();

    $this
        ->delete(route('igniter.api.customers.destroy', [$customer->getKey()]))
        ->assertStatus(204);
});