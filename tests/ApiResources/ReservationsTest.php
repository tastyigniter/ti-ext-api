<?php

namespace Igniter\Api\Tests\ApiResources;

use Igniter\Reservation\Models\Reservation;
use Igniter\User\Models\User;
use Laravel\Sanctum\Sanctum;

it('returns all reservations', function() {
    Sanctum::actingAs(User::factory()->create(), ['reservations:*']);
    Reservation::factory()->count(3)->create();

    $this
        ->get(route('igniter.api.reservations.index'))
        ->assertOk()
        ->assertJsonPath('data.0.attributes.email', Reservation::first()->email);
});

it('shows a reservation', function() {
    Sanctum::actingAs(User::factory()->create(), ['reservations:*']);
    $reservation = Reservation::factory()->create();

    $this
        ->get(route('igniter.api.reservations.show', [$reservation->getKey()]))
        ->assertOk()
        ->assertJsonPath('data.attributes.email', $reservation->email);
});

it('creates a reservation', function() {
    Sanctum::actingAs(User::factory()->create(), ['reservations:*']);

    $this
        ->post(route('igniter.api.reservations.store'), [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test-user@domain.tld',
            'telephone' => '1234567890',
            'reserve_date' => '2022-12-31',
            'reserve_time' => '12:00',
            'guest_num' => 2,
            'status_id' => 1,
            'location_id' => 1,
            'tables' => [],
        ])
        ->assertCreated()
        ->assertJsonPath('data.attributes.email', 'test-user@domain.tld');
});

it('updates a reservation', function() {
    Sanctum::actingAs(User::factory()->create(), ['reservations:*']);
    $reservation = Reservation::factory()->create();

    $this
        ->put(route('igniter.api.reservations.update', [$reservation->getKey()]), [
            'reserve_time' => '23:00',
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test-user@domain.tld',
            'telephone' => '1234567890',
            'reserve_date' => '2022-12-31',
            'guest_num' => 2,
            'status_id' => 1,
            'location_id' => 1,
        ])
        ->assertOk()
        ->assertJsonPath('data.attributes.reserve_time', '23:00');
});

it('deletes a reservation', function() {
    Sanctum::actingAs(User::factory()->create(), ['reservations:*']);
    $reservation = Reservation::factory()->create();

    $this
        ->delete(route('igniter.api.reservations.destroy', [$reservation->getKey()]))
        ->assertStatus(204);
});
