<?php

declare(strict_types=1);

namespace Igniter\Api\Tests\Traits;

use Igniter\Api\Traits\AppliesAssigneeScope;
use Igniter\Cart\Models\Order;
use Igniter\Flame\Database\Builder;
use Igniter\User\Models\Customer;
use Igniter\User\Models\User;
use Illuminate\Database\Eloquent\Model;
use Mockery;

it('does not apply assignee scope for customer users', function(): void {
    $traitObject = new class
    {
        use AppliesAssigneeScope;

        public function testApplyAssigneeScope(\Illuminate\Database\Eloquent\Builder $query): void
        {
            $this->applyAssigneeScope($query);
        }
    };
    $query = Mockery::mock(Builder::class);
    $query->shouldReceive('getModel')->andReturn(Mockery::mock(Order::class)->makePartial());
    $query->shouldReceive('whereInAssignToGroup')->never();
    $query->shouldReceive('whereAssignTo')->never();
    request()->setUserResolver(fn() => Mockery::mock(Customer::class)->makePartial());

    $traitObject->testApplyAssigneeScope($query);
});

it('does not apply assignee scope when model is not assignable', function(): void {
    $traitObject = new class
    {
        use AppliesAssigneeScope;

        public function testApplyAssigneeScope(\Illuminate\Database\Eloquent\Builder $query): void
        {
            $this->applyAssigneeScope($query);
        }
    };
    $query = Mockery::mock(Builder::class);
    $query->shouldReceive('getModel')->andReturn(Mockery::mock(Model::class));
    $query->shouldReceive('whereInAssignToGroup')->never();
    $query->shouldReceive('whereAssignTo')->never();
    $user = Mockery::mock(User::class)->makePartial();
    $user->shouldReceive('hasGlobalAssignableScope')->never();
    request()->setUserResolver(fn() => $user);

    $traitObject->testApplyAssigneeScope($query);
});

it('does not apply assignee scope when user has global scope', function(): void {
    $traitObject = new class
    {
        use AppliesAssigneeScope;

        public function testApplyAssigneeScope(\Illuminate\Database\Eloquent\Builder $query): void
        {
            $this->applyAssigneeScope($query);
        }
    };
    $query = Mockery::mock(Builder::class);
    $query->shouldReceive('getModel')->andReturn(Mockery::mock(Order::class)->makePartial());
    $query->shouldReceive('whereInAssignToGroup')->never();
    $query->shouldReceive('whereAssignTo')->never();
    $user = Mockery::mock(User::class)->makePartial();
    $user->shouldReceive('hasGlobalAssignableScope')->andReturn(true);
    request()->setUserResolver(fn() => $user);

    $traitObject->testApplyAssigneeScope($query);
});

it('applies group scope when user has group assignable scope', function(): void {
    $traitObject = new class
    {
        use AppliesAssigneeScope;

        public function testApplyAssigneeScope(\Illuminate\Database\Eloquent\Builder $query): void
        {
            $this->applyAssigneeScope($query);
        }
    };
    $query = Mockery::mock(Builder::class);
    $query->shouldReceive('getModel')->andReturn(Mockery::mock(Order::class)->makePartial());
    $user = Mockery::mock(User::class)->makePartial();
    $user->shouldReceive('hasGlobalAssignableScope')->andReturn(false);
    $user->shouldReceive('hasRestrictedAssignableScope')->andReturn(false);
    $user->shouldReceive('extendableGet')->with('groups')->andReturn(collect([['user_group_id' => 1], ['user_group_id' => 2]]));
    $query->shouldReceive('whereInAssignToGroup')->with([1, 2])->once();
    $query->shouldReceive('whereAssignTo')->never();
    request()->setUserResolver(fn() => $user);

    $traitObject->testApplyAssigneeScope($query);
});

it('applies group and assignee scope when user has restricted scope', function(): void {
    $traitObject = new class
    {
        use AppliesAssigneeScope;

        public function testApplyAssigneeScope(\Illuminate\Database\Eloquent\Builder $query): void
        {
            $this->applyAssigneeScope($query);
        }
    };
    $query = Mockery::mock(Builder::class);
    $query->shouldReceive('getModel')->andReturn(Mockery::mock(Order::class)->makePartial());
    $user = Mockery::mock(User::class)->makePartial();
    $user->shouldReceive('hasGlobalAssignableScope')->andReturn(false);
    $user->shouldReceive('hasRestrictedAssignableScope')->andReturn(true);
    $user->shouldReceive('getKey')->andReturn(5);
    $user->shouldReceive('extendableGet')->with('groups')->andReturn(collect([['user_group_id' => 3]]));
    $query->shouldReceive('whereInAssignToGroup')->with([3])->once();
    $query->shouldReceive('whereAssignTo')->with(5)->once();
    request()->setUserResolver(fn() => $user);

    $traitObject->testApplyAssigneeScope($query);
});

it('extendQuery delegates to applyAssigneeScope', function(): void {
    $traitObject = new class
    {
        use AppliesAssigneeScope;

        public function testExtendQuery(\Illuminate\Database\Eloquent\Builder $query): void
        {
            $this->extendQuery($query);
        }
    };
    $query = Mockery::mock(Builder::class);
    $query->shouldReceive('getModel')->andReturn(Mockery::mock(Order::class)->makePartial())->once();
    $query->shouldReceive('whereInAssignToGroup')->never();
    $query->shouldReceive('whereAssignTo')->never();
    $user = Mockery::mock(User::class)->makePartial();
    $user->shouldReceive('hasGlobalAssignableScope')->andReturn(true)->once();
    request()->setUserResolver(fn() => $user);

    $traitObject->testExtendQuery($query);

    expect(true)->toBeTrue();
});
