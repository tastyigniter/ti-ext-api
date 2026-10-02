<?php

declare(strict_types=1);

namespace Igniter\Api\Tests\ApiResources;

use Igniter\Admin\Models\Status;
use Igniter\Api\ApiResources\AppSettings;
use Igniter\System\Classes\ExtensionManager;
use Igniter\User\Models\User;
use Laravel\Sanctum\Sanctum;
use Mockery;

it('returns app settings for the signed in staff user', function(): void {
    $user = User::factory()->create();
    $received = Status::factory()->create([
        'status_for' => 'order',
        'status_name' => 'Received',
        'status_color' => '#111111',
    ]);
    $preparing = Status::factory()->create([
        'status_for' => 'order',
        'status_name' => 'Preparing',
        'status_color' => '#222222',
    ]);
    $completed = Status::factory()->create([
        'status_for' => 'order',
        'status_name' => 'Completed',
        'status_color' => '#333333',
    ]);
    $accepted = Status::factory()->create([
        'status_for' => 'order',
        'status_name' => 'Accepted',
        'status_color' => '#444444',
    ]);
    $canceled = Status::factory()->create([
        'status_for' => 'order',
        'status_name' => 'Canceled',
        'status_color' => '#555555',
    ]);
    $booked = Status::factory()->create([
        'status_for' => 'reservation',
        'status_name' => 'Booked',
        'status_color' => '#666666',
    ]);

    setting()->set([
        'enable_status_workflow' => true,
        'limit_users' => [0, (string)$user->getKey()],
        'default_order_status' => $received->getKey(),
        'processing_order_status' => [$preparing->getKey(), null, '', 0, 999999],
        'completed_order_status' => [],
        'accepted_order_status' => $accepted->getKey(),
        'canceled_order_status' => $canceled->getKey(),
        'default_reservation_status' => [$booked->getKey()],
        'confirmed_reservation_status' => null,
        'canceled_reservation_status' => '',
        'rejected_reasons' => [
            'skip',
            ['code' => '   '],
            ['code' => 'closed', 'comment' => 'Kitchen closed', 'status_id' => $canceled->getKey()],
            ['code' => 'other'],
        ],
        'delay_times' => [
            'skip',
            ['time' => 0, 'comment' => 'Now'],
            ['time' => 15, 'comment' => 'Fifteen minutes'],
            ['time' => 30],
        ],
    ]);

    Sanctum::actingAs($user, ['app_settings:*']);

    $this->get(route('igniter.api.app_settings.index'))
        ->assertOk()
        ->assertJsonPath('data.type', 'app_settings')
        ->assertJsonPath('data.id', 'settings')
        ->assertJsonPath('data.attributes.order_status.workflow_enabled', true)
        ->assertJsonPath('data.attributes.order_status.groups.new.0.name', 'Received')
        ->assertJsonPath('data.attributes.order_status.groups.processing.0.id', (string)$preparing->getKey())
        ->assertJsonPath('data.attributes.order_status.groups.completed', [])
        ->assertJsonPath('data.attributes.order_status.accepted_order_status.name', 'Accepted')
        ->assertJsonPath('data.attributes.order_status.canceled_order_status.name', 'Canceled')
        ->assertJsonPath('data.attributes.order_status.rejected_reasons.0.code', 'closed')
        ->assertJsonPath('data.attributes.order_status.rejected_reasons.0.status.name', 'Canceled')
        ->assertJsonPath('data.attributes.order_status.rejected_reasons.1.code', 'other')
        ->assertJsonPath('data.attributes.order_status.rejected_reasons.1.status', null)
        ->assertJsonPath('data.attributes.order_status.delay_times.0', ['time' => 15, 'comment' => 'Fifteen minutes'])
        ->assertJsonPath('data.attributes.order_status.delay_times.1', ['time' => 30, 'comment' => ''])
        ->assertJsonPath('data.attributes.reservation_status.groups.new.0.name', 'Booked')
        ->assertJsonPath('data.attributes.reservation_status.groups.confirmed', [])
        ->assertJsonPath('data.attributes.reservation_status.groups.canceled', []);
});

it('reports workflow access from the status settings', function(): void {
    $settings = new AppSettings;
    $userId = 8;

    setting()->set(['enable_status_workflow' => false, 'limit_users' => [$userId]]);
    expect(callProtectedMethod($settings, 'workflowEnabledFor', [$userId]))->toBeFalse();

    setting()->set(['enable_status_workflow' => true, 'limit_users' => []]);
    expect(callProtectedMethod($settings, 'workflowEnabledFor', [$userId]))->toBeTrue();

    setting()->set(['enable_status_workflow' => true, 'limit_users' => [0, '']]);
    expect(callProtectedMethod($settings, 'workflowEnabledFor', [$userId]))->toBeTrue();

    setting()->set(['enable_status_workflow' => true, 'limit_users' => [4]]);
    expect(callProtectedMethod($settings, 'workflowEnabledFor', [$userId]))->toBeFalse()
        ->and(callProtectedMethod($settings, 'workflowEnabledFor', [null]))->toBeFalse();

    setting()->set(['enable_status_workflow' => true, 'limit_users' => [$userId]]);
    expect(callProtectedMethod($settings, 'workflowEnabledFor', [$userId]))->toBeTrue();
});

it('returns no attributes for a guest user id', function(): void {
    setting()->set([
        'enable_status_workflow' => true,
        'limit_users' => [4],
        'default_order_status' => null,
        'rejected_reasons' => [],
        'delay_times' => [],
    ]);

    $response = (new AppSettings)->index();

    expect($response['data']['attributes']['order_status']['workflow_enabled'])->toBeFalse()
        ->and($response['data']['attributes']['order_status']['groups']['new'])->toBe([]);
});

it('checks whether an extension is installed and enabled', function(): void {
    $settings = new AppSettings;
    $manager = resolve(ExtensionManager::class);
    $installed = $manager->hasExtension('igniter.api') && !$manager->isDisabled('igniter.api');

    expect(callProtectedMethod($settings, 'extensionInstalled', ['igniter.api']))->toBe($installed)
        ->and(callProtectedMethod($settings, 'extensionInstalled', ['igniterlabs.not-installed']))->toBeFalse();

    $disabled = Mockery::mock(ExtensionManager::class);
    $disabled->shouldReceive('hasExtension')->once()->with('igniterlabs.dinein')->andReturn(true);
    $disabled->shouldReceive('isDisabled')->once()->with('igniterlabs.dinein')->andReturn(true);
    app()->instance(ExtensionManager::class, $disabled);

    expect(callProtectedMethod($settings, 'extensionInstalled', ['igniterlabs.dinein']))->toBeFalse();
});
