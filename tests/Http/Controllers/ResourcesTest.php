<?php

declare(strict_types=1);

namespace Igniter\Api\Tests\Http\Controllers;

use Igniter\Api\Models\Resource;

it('loads resources page', function(): void {
    actingAsSuperUser()
        ->get(route('igniter.api.resources'))
        ->assertOk();
});

it('fails to load create resource page', function(): void {
    actingAsSuperUser()
        ->get(route('igniter.api.resources', ['slug' => 'create']))
        ->assertStatus(500);

    actingAsSuperUser()
        ->post(route('igniter.api.resources', ['slug' => 'create']), [], [
            'X-Requested-With' => 'XMLHttpRequest',
            'X-IGNITER-REQUEST-HANDLER' => 'onSave',
        ])
        ->assertStatus(500);
});

it('loads edit resource page', function(): void {
    $resource = Resource::factory()->create([
        'endpoint' => 'categories',
    ]);

    actingAsSuperUser()
        ->get(route('igniter.api.resources', ['slug' => 'edit/'.$resource->getKey()]))
        ->assertOk();
});

it('loads resource preview page', function(): void {
    $resource = Resource::factory()->create([
        'endpoint' => 'categories',
    ]);

    actingAsSuperUser()
        ->get(route('igniter.api.resources', ['slug' => 'preview/'.$resource->getKey()]))
        ->assertOk();
});

it('updates resource', function(): void {
    $resource = Resource::factory()->create();

    actingAsSuperUser()
        ->post(route('igniter.api.resources', ['slug' => 'edit/'.$resource->getKey()]), [
            'Resource' => [
                'name' => 'Updated Resource',
                'endpoint' => 'updated-endpoint',
                'description' => 'Updated Resource Description',
            ],
        ], [
            'X-Requested-With' => 'XMLHttpRequest',
            'X-IGNITER-REQUEST-HANDLER' => 'onSave',
        ]);

    expect(Resource::find($resource->getKey()))->name->toBe('Updated Resource')
        ->endpoint->not->toBe('updated-endpoint')
        ->endpoint->toBe($resource->endpoint);
});

it('deletes resource', function(): void {
    $resource = Resource::factory()->create();

    actingAsSuperUser()
        ->post(route('igniter.api.resources', ['slug' => 'edit/'.$resource->getKey()]), [], [
            'X-Requested-With' => 'XMLHttpRequest',
            'X-IGNITER-REQUEST-HANDLER' => 'onDelete',
        ]);

    expect(Resource::find($resource->getKey()))->toBeNull();
});

it('only offers staff authorization on the staff resource', function(): void {
    $resource = Resource::query()->where('endpoint', 'users')->firstOrFail();

    actingAsSuperUser()
        ->get(route('igniter.api.resources', ['slug' => 'edit/'.$resource->getKey()]))
        ->assertOk()
        ->assertSee('value="admin"', false)
        ->assertDontSee('value="customer"', false)
        ->assertDontSee('value="guest"', false);
});

it('rejects an authorization outside the allowed list', function(): void {
    $resource = Resource::query()->where('endpoint', 'users')->firstOrFail();

    actingAsSuperUser()
        ->post(route('igniter.api.resources', ['slug' => 'edit/'.$resource->getKey()]), [
            'Resource' => [
                'name' => 'Staff',
                'description' => 'An API resource for staff',
                'meta' => ['actions' => ['index'], 'authorization' => ['index' => 'all']],
            ],
        ], [
            'X-Requested-With' => 'XMLHttpRequest',
            'X-IGNITER-REQUEST-HANDLER' => 'onSave',
        ]);

    expect(Resource::find($resource->getKey())->meta['authorization']['index'])->toBe('admin');
});

it('saves an authorization inside the allowed list', function(): void {
    $resource = Resource::query()->where('endpoint', 'categories')->firstOrFail();

    actingAsSuperUser()
        ->post(route('igniter.api.resources', ['slug' => 'edit/'.$resource->getKey()]), [
            'Resource' => [
                'name' => 'Categories',
                'description' => 'An API resource for categories',
                'meta' => ['actions' => ['index'], 'authorization' => ['index' => 'customer']],
            ],
        ], [
            'X-Requested-With' => 'XMLHttpRequest',
            'X-IGNITER-REQUEST-HANDLER' => 'onSave',
        ]);

    expect(Resource::find($resource->getKey())->meta['authorization']['index'])->toBe('customer');
});
