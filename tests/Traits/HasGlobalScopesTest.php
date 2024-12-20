<?php

namespace Igniter\Api\Tests\Traits;

use Igniter\Api\Traits\HasGlobalScopes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Scope;
use Mockery;

it('adds a global scope using a string and closure', function() {
    $traitObject = new class
    {
        use HasGlobalScopes;
    };
    $closure = function() {};
    $result = $traitObject->addGlobalScope('testScope', $closure);

    expect($result)->toBe($closure);
});

it('adds a global scope using a closure', function() {
    $traitObject = new class
    {
        use HasGlobalScopes;
    };
    $closure = function() {};
    $result = $traitObject->addGlobalScope($closure);

    expect($result)->toBe($closure);
});

it('adds a global scope using a Scope instance', function() {
    $traitObject = new class
    {
        use HasGlobalScopes;
    };
    $scope = Mockery::mock(Scope::class);
    $result = $traitObject->addGlobalScope($scope);

    expect($result)->toBe($scope);
});

it('throws an exception when adding an invalid global scope', function() {
    $traitObject = new class
    {
        use HasGlobalScopes;
    };

    expect(function() use ($traitObject) {
        $traitObject->addGlobalScope('invalidScope');
    })->toThrow(\InvalidArgumentException::class, 'Global scope must be an instance of Closure or Scope.');
});

it('applies all registered global scopes to the query', function() {
    $traitObject = new class
    {
        use HasGlobalScopes;

        public function testApplyScopes($query)
        {
            $this->applyScopes($query);
        }
    };
    $query = Mockery::mock(Builder::class);
    $scope = Mockery::mock(Scope::class);
    $traitObject->addGlobalScope($scope);

    $query->shouldReceive('withGlobalScope')->with(Mockery::any(), [get_class($scope) => $scope])->once();
    $traitObject->testApplyScopes($query);
});
