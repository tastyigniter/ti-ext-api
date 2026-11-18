<?php

declare(strict_types=1);

namespace Igniter\Api\Tests\Classes;

use Igniter\Api\Classes\ApiController;
use Igniter\User\Models\Customer;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Facades\Event;
use Mockery;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

it('calls action and returns json response if not Responsable', function(): void {
    $controller = Mockery::mock(ApiController::class)->makePartial()->shouldAllowMockingProtectedMethods();
    $controller->allowedActions = ['testAction' => true];
    $controller->shouldReceive('checkAction')->andReturnTrue();
    $controller->shouldReceive('token')->andReturnTrue();
    $controller->shouldReceive('getAbilities')->andReturn([]);
    $controller->shouldReceive('testAction')->andReturn(['key' => 'value']);
    $controller->shouldReceive('isResponsable')->andReturnFalse();

    $response = $controller->callAction('testAction');

    expect($response->getContent())->toBe(json_encode(['key' => 'value']));
});

it('calls action and returns Responsable response', function(): void {
    $controller = Mockery::mock(ApiController::class)->makePartial()->shouldAllowMockingProtectedMethods();
    $controller->allowedActions = ['testAction' => true];
    $controller->shouldReceive('checkAction')->andReturnTrue();
    $controller->shouldReceive('token')->andReturnFalse();
    $responseMock = Mockery::mock(Responsable::class);
    $controller->shouldReceive('testAction')->andReturn($responseMock);
    $controller->shouldReceive('isResponsable')->andReturnTrue();

    expect($controller->callAction('testAction'))->toBe($responseMock);
});

it('fires beforeAction after authorizing the token and before running the action', function(): void {
    $calls = [];
    Event::listen('api.controller.beforeAction', function($controller, $method, $parameters) use (&$calls): void {
        $calls[] = ['beforeAction', $method, $parameters];
    });

    $controller = Mockery::mock(ApiController::class)->makePartial()->shouldAllowMockingProtectedMethods();
    $controller->allowedActions = ['testAction' => true];
    $controller->shouldReceive('checkAction')->andReturnTrue();
    $controller->shouldReceive('token')->andReturnTrue();
    $controller->shouldReceive('authorizeToken')->once()->andReturnUsing(function() use (&$calls): void {
        $calls[] = ['authorizeToken'];
    });
    $controller->shouldReceive('testAction')->andReturnUsing(function() use (&$calls): array {
        $calls[] = ['testAction'];

        return [];
    });

    $controller->callAction('testAction', ['id' => 1]);

    expect($calls)->toBe([['authorizeToken'], ['beforeAction', 'testAction', ['id' => 1]], ['testAction']]);
});

it('calls action returns 404 response when checkAction fails', function(): void {
    $controller = Mockery::mock(ApiController::class)->makePartial()->shouldAllowMockingProtectedMethods();
    $controller->allowedActions = ['testAction' => true];
    $controller->shouldReceive('checkAction')->andReturnFalse();
    $controller->shouldReceive('isResponsable')->andReturnFalse();

    expect($controller->callAction('testAction')->getStatusCode())->toBe(404);
});

it('calls action throws exception when authorization fails', function(): void {
    $controller = Mockery::mock(ApiController::class)->makePartial()->shouldAllowMockingProtectedMethods();
    $controller->allowedActions = ['testAction' => true];
    $controller->shouldReceive('checkAction')->andReturnTrue();
    $controller->shouldReceive('token')->andReturnTrue();
    $controller->shouldReceive('getAbilities')->andReturn(['orders:*']);
    $controller->shouldReceive('tokenCan')->with('orders:*')->andReturnFalse()->once();
    $controller->shouldReceive('tokenCan')->with('orders:testAction')->andReturnFalse()->once();
    $controller->shouldReceive('testAction')->andReturn(['key' => 'value']);
    $controller->shouldReceive('isResponsable')->andReturnFalse();

    expect(fn() => $controller->callAction('testAction'))->toThrow(AccessDeniedHttpException::class);
});

it('calls action when the token has the ability for that action', function(): void {
    $controller = Mockery::mock(ApiController::class)->makePartial()->shouldAllowMockingProtectedMethods();
    $controller->allowedActions = ['testAction' => true];
    $controller->shouldReceive('checkAction')->andReturnTrue();
    $controller->shouldReceive('token')->andReturnTrue();
    $controller->shouldReceive('user')->andReturnNull();
    $controller->shouldReceive('getAbilities')->andReturn(['orders:*']);
    $controller->shouldReceive('tokenCan')->with('orders:*')->andReturnFalse();
    $controller->shouldReceive('tokenCan')->with('orders:testAction')->andReturnTrue();
    $controller->shouldReceive('testAction')->andReturn(['key' => 'value']);
    $controller->shouldReceive('isResponsable')->andReturnFalse();

    expect($controller->callAction('testAction')->getData(true))->toBe(['key' => 'value']);
});

it('calls action throws exception when customer requests admin abilities', function(): void {
    $controller = Mockery::mock(ApiController::class)->makePartial()->shouldAllowMockingProtectedMethods();
    $controller->allowedActions = ['testAction' => true];
    $controller->shouldReceive('checkAction')->andReturnTrue();
    $controller->shouldReceive('token')->andReturnTrue();
    $controller->shouldReceive('getAbilities')->andReturn(['staff:*']);
    $controller->shouldReceive('user')->andReturn(Mockery::mock(Customer::class));
    $controller->shouldReceive('tokenCan')->never();

    expect(fn() => $controller->callAction('testAction'))->toThrow(AccessDeniedHttpException::class);
});

it('returns false if action is not in allowedActions', function(): void {
    $controller = Mockery::mock(ApiController::class)->makePartial();
    $controller->allowedActions = [];

    expect($controller->checkAction('testAction'))->toBeFalse();
});

it('returns false if method does not exist', function(): void {
    $controller = Mockery::mock(ApiController::class)->makePartial();
    $controller->allowedActions = ['testAction' => true];
    $controller->shouldReceive('methodExists')->andReturnFalse();

    expect($controller->checkAction('testAction'))->toBeFalse();
});

it('returns false if method is not found', function(): void {
    $controller = new class extends ApiController
    {
        public array $allowedActions = ['testAction' => true];
    };

    $result = $controller->checkAction('testAction');

    expect($result)->toBeFalse();
});

it('returns false if method is not public', function(): void {
    $controller = new class extends ApiController
    {
        public array $allowedActions = ['testAction' => true];

        protected function testAction(): array
        {
            return ['key' => 'value'];
        }
    };

    $result = $controller->checkAction('testAction');

    expect($result)->toBeFalse();
});

it('returns true if method is public', function(): void {
    $controller = new class extends ApiController
    {
        public array $allowedActions = ['testAction' => true];

        public function testAction(): array
        {
            return ['key' => 'value'];
        }
    };

    $result = $controller->checkAction('testAction');

    expect($result)->toBeTrue();
});
