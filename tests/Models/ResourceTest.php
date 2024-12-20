<?php

namespace Igniter\Api\Tests\Models;

use Igniter\Api\Models\Resource;
use Igniter\Flame\Database\Traits\HasPermalink;
use Mockery;

it('returns default action definition when no meta options are set', function() {
    $resource = new class(['endpoint' => 'test-endpoint']) extends Resource
    {
        public function testGet()
        {
            return self::$defaultActionDefinition;
        }
    };
    $result = $resource->getMetaOptions();

    expect($result)->toBe($resource->testGet());
});

it('returns base endpoint attribute correctly', function() {
    $resource = new Resource(['endpoint' => 'test-endpoint']);
    $result = $resource->getBaseEndpointAttribute(null);

    expect($result)->toBe('/api/test-endpoint');
});

it('returns controller attribute correctly', function() {
    $resource = Mockery::mock(Resource::class)->makePartial();
    $resource->shouldReceive('listRegisteredResources')->andReturn([
        'test-endpoint' => ['controller' => 'TestController'],
    ]);
    $resource->endpoint = 'test-endpoint';

    $result = $resource->getControllerAttribute();

    expect($result)->toBe('TestController');
});

it('syncs all resources correctly', function() {
    Resource::factory()->create(['endpoint' => 'endpoint1', 'is_custom' => true]);
    Resource::factory()->create(['endpoint' => 'endpoint2']);
    Resource::clearInternalCache();

    Resource::syncAll();

    expect(Resource::where('endpoint', 'endpoint2')->exists())->toBeFalse();
});

it('returns all resources keyed by endpoint', function() {
    expect(Resource::getResources())->not->toBeEmpty();
});

it('lists all resources correctly', function() {
    Resource::clearInternalCache();
    (new Resource)->registerCallback(function(Resource $resource) {
        $resource->registerResources([
            'endpoint1' => ['controller' => 'TestController'],
            'endpoint2' => 'TestController',
        ]);
    });

    expect(Resource::listResources())->not->toBeEmpty();
    Resource::clearInternalCache();
});

it('configures resource model correctly', function() {
    $resource = new Resource;

    expect(class_uses_recursive($resource))->toContain(HasPermalink::class)
        ->and($resource->getTable())->toBe('igniter_api_resources')
        ->and($resource->getFillable())->toEqual(['name', 'description', 'endpoint', 'model', 'meta'])
        ->and($resource->getCasts()['meta'])->toBe('array')
        ->and($resource->permalinkable())->toBe([
            'endpoint' => [
                'source' => 'name',
            ],
        ]);
});
