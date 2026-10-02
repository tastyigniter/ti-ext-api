<?php

declare(strict_types=1);

namespace Igniter\Api\Tests\ApiResources;

use Igniter\User\Classes\Notification;
use Igniter\User\Models\User;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;

it('returns notifications for the signed in user', function(): void {
    $user = User::factory()->create();
    $other = User::factory()->create();
    Sanctum::actingAs($user, ['notifications:*']);

    $own = $user->notifications()->create([
        'id' => (string)Str::uuid(),
        'type' => Notification::class,
        'data' => ['title' => 'New order', 'message' => 'Order 10 is waiting'],
    ]);
    $other->notifications()->create([
        'id' => (string)Str::uuid(),
        'type' => Notification::class,
        'data' => ['title' => 'Hidden', 'message' => 'Not yours'],
    ]);

    $this->get(route('igniter.api.notifications.index'))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', (string)$own->getKey())
        ->assertJsonPath('data.0.attributes.title', 'New order')
        ->assertJsonPath('data.0.attributes.message', 'Order 10 is waiting');
});

it('marks a notification read', function(): void {
    $user = User::factory()->create();
    Sanctum::actingAs($user, ['notifications:*']);
    $notification = $user->notifications()->create([
        'id' => (string)Str::uuid(),
        'type' => Notification::class,
        'data' => ['title' => 'New order', 'message' => 'Waiting'],
    ]);

    $this->put(route('igniter.api.notifications.update', [$notification->getKey()]), [
        'read' => true,
    ])
        ->assertOk()
        ->assertJsonPath('data.attributes.title', 'New order');

    expect($notification->refresh()->read_at)->not->toBeNull();
});

it('deletes a notification', function(): void {
    $user = User::factory()->create();
    Sanctum::actingAs($user, ['notifications:*']);
    $notification = $user->notifications()->create([
        'id' => (string)Str::uuid(),
        'type' => Notification::class,
        'data' => ['title' => 'New order', 'message' => 'Waiting'],
    ]);

    $this->delete(route('igniter.api.notifications.destroy', [$notification->getKey()]))
        ->assertNoContent();

    expect($user->notifications()->count())->toBe(0);
});

it('does not show another users notification', function(): void {
    $user = User::factory()->create();
    $other = User::factory()->create();
    Sanctum::actingAs($user, ['notifications:*']);
    $notification = $other->notifications()->create([
        'id' => (string)Str::uuid(),
        'type' => Notification::class,
        'data' => ['title' => 'Hidden', 'message' => 'Not yours'],
    ]);

    $this->get(route('igniter.api.notifications.show', [$notification->getKey()]))
        ->assertNotFound();
});
