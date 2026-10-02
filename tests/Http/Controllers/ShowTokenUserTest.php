<?php

declare(strict_types=1);

namespace Igniter\Api\Tests\Http\Controllers;

use Igniter\Local\Models\Location;
use Igniter\User\Models\Customer;
use Igniter\User\Models\User;
use Igniter\User\Models\UserGroup;
use Igniter\User\Models\UserRole;
use Laravel\Sanctum\Sanctum;

it('show authenticated user', function(): void {
    $role = UserRole::factory()->create(['name' => 'Manager']);
    $group = UserGroup::factory()->create(['user_group_name' => 'Front of house']);
    $location = Location::factory()->create(['location_name' => 'Downtown']);
    $user = User::factory()->create([
        'user_role_id' => $role->getKey(),
        'sale_permission' => 2,
    ]);
    $user->groups()->attach($group);
    $user->locations()->attach($location);

    Sanctum::actingAs($user, ['users:*']);

    $this->get(route('igniter.api.token.user'))
        ->assertOk()
        ->assertJsonPath('user.user_id', $user->getKey())
        ->assertJsonPath('user.sale_permission', 2)
        ->assertJsonPath('user.role.name', 'Manager')
        ->assertJsonPath('user.groups.0.name', 'Front of house')
        ->assertJsonPath('user.assigned_locations.0.name', 'Downtown');
});

it('show authenticated customer', function(): void {
    Sanctum::actingAs(Customer::factory()->create(), ['customers:*']);

    $this->get(route('igniter.api.token.user'))
        ->assertOk()
        ->assertJsonStructure(['status_code', 'user']);
});

it('returns null for unauthenticated user', function(): void {
    $this->get(route('igniter.api.token.user'))
        ->assertUnauthorized();
});
