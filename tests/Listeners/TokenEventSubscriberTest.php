<?php

declare(strict_types=1);

namespace Igniter\Api\Tests\Listeners;

use Igniter\Api\Listeners\TokenEventSubscriber;
use Igniter\Api\Models\Token;
use Igniter\User\Models\User;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as RouteFacade;
use Laravel\Sanctum\Events\TokenAuthenticated;
use Mockery;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

beforeEach(function(): void {
    $this->subscriber = new TokenEventSubscriber;
    $this->token = Token::factory()->create();
    $this->event = new TokenAuthenticated($this->token);
    $this->route = Mockery::mock(Route::class);
    request()->setRouteResolver(fn() => $this->route);
});

it('returns access token for allowed group all', function(): void {
    RouteFacade::shouldReceive('currentRouteName')->andReturn('api.categories.index');
    $this->route->shouldReceive('currentRouteName')->andReturn('api.categories.index');
    $this->route->shouldReceive('getActionMethod')->andReturn('index');

    $result = $this->subscriber->handleTokenAuthenticated($this->event);

    expect($result)->token->toBe($this->token->token);
});

it('throws unauthorized exception for missing access token', function(): void {
    RouteFacade::shouldReceive('currentRouteName')->andReturn('api.categories.store');
    $this->route->shouldReceive('currentRouteName')->andReturn('api.categories.store');
    $this->route->shouldReceive('getActionMethod')->andReturn('store');

    $this->event->token = null;

    $this->expectException(UnauthorizedHttpException::class);
    $this->expectExceptionMessage(lang('igniter.api::default.alert_auth_failed'));

    $this->subscriber->handleTokenAuthenticated($this->event);
});

it('throws access denied exception for restricted group', function(): void {
    RouteFacade::shouldReceive('currentRouteName')->andReturn('api.categories.store');
    $this->route->shouldReceive('currentRouteName')->andReturn('api.categories.store');
    $this->route->shouldReceive('getActionMethod')->andReturn('store');

    $this->event->token->tokenable_type = 'customers';

    $this->expectException(AccessDeniedHttpException::class);
    $this->expectExceptionMessage(lang('igniter.api::default.alert_auth_restricted'));

    $this->subscriber->handleTokenAuthenticated($this->event);
});

it('throws access denied exception for guest group', function(): void {
    $subscriber = Mockery::mock(TokenEventSubscriber::class)->makePartial()->shouldAllowMockingProtectedMethods();
    $subscriber->shouldReceive('getAllowedGroup')->andReturn('guest');

    $this->expectException(AccessDeniedHttpException::class);
    $this->expectExceptionMessage(lang('igniter.api::default.alert_auth_restricted'));

    $subscriber->handleTokenAuthenticated($this->event);
});

it('throws access denied exception for customer group', function(): void {
    $subscriber = Mockery::mock(TokenEventSubscriber::class)->makePartial()->shouldAllowMockingProtectedMethods();
    $subscriber->shouldReceive('getAllowedGroup')->andReturn('customer');
    $this->event->token->tokenable_type = 'users';

    $this->expectException(AccessDeniedHttpException::class);
    $this->expectExceptionMessage(lang('igniter.api::default.alert_auth_restricted'));

    $subscriber->handleTokenAuthenticated($this->event);
});

it('returns tokenable for valid access token', function(): void {
    RouteFacade::shouldReceive('currentRouteName')->andReturn('api.categories.store');
    $this->route->shouldReceive('currentRouteName')->andReturn('api.categories.store');
    $this->route->shouldReceive('getActionMethod')->andReturn('store');

    $this->event->token = $this->token;
    $this->event->token->tokenable = User::factory()->create();

    $tokenable = $this->subscriber->handleTokenAuthenticated($this->event);

    expect($tokenable)->toBeInstanceOf(User::class);
});
