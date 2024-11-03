<?php

namespace Igniter\Api\Tests\Classes;

use Igniter\Api\Classes\AbstractRepository;
use Igniter\Flame\Database\Model;
use Illuminate\Support\Facades\DB;
use Mockery;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

beforeEach(function() {
    $this->repository = Mockery::mock(AbstractRepository::class)->makePartial()->shouldAllowMockingProtectedMethods();
    $this->model = Mockery::mock(Model::class);
});

it('finds a record by id', function() {
    $this->repository->shouldReceive('createModel')->andReturn($this->model);
    $this->repository->shouldReceive('prepareQuery')->andReturn($this->model);
    $this->model->shouldReceive('find')->with(1, ['*'])->andReturn($this->model);

    $result = $this->repository->find(1);

    expect($result)->toBe($this->model);
});

it('throws exception when record not found by id', function() {
    $this->repository->shouldReceive('createModel')->andReturn($this->model);
    $this->repository->shouldReceive('prepareQuery')->andReturn($this->model);
    $this->model->shouldReceive('find')->with(1, ['*'])->andReturn(null);

    $this->expectException(NotFoundHttpException::class);

    $this->repository->find(1);
});

it('finds a record by attribute', function() {
    $this->repository->shouldReceive('createModel')->andReturn($this->model);
    $this->repository->shouldReceive('prepareQuery')->andReturn($this->model);
    $this->model->shouldReceive('where')->with('name', 'test')->andReturnSelf();
    $this->model->shouldReceive('first')->with(['*'])->andReturn($this->model);

    $result = $this->repository->findBy('name', 'test');

    expect($result)->toBe($this->model);
});

it('creates a new record', function() {
    $attributes = ['name' => 'test'];
    $this->repository->shouldReceive('createModel')->andReturn($this->model);
    $this->repository->shouldReceive('setModelAttributes')->with($this->model, $attributes);
    $this->repository->shouldReceive('setCustomerAwareAttributes')->with($this->model);
    $this->repository->shouldReceive('fireSystemEvent')->with('api.repository.beforeCreate', [$this->model, $attributes]);
    $this->repository->shouldReceive('fireSystemEvent')->with('api.repository.afterCreate', [$this->model, true]);
    $this->model->shouldReceive('reload');

    DB::shouldReceive('transaction')->andReturnUsing(function($callback) {
        $callback();
    });

    $result = $this->repository->create($this->model, $attributes);

    expect($result)->toBe($this->model);
});

it('updates an existing record', function() {
    $attributes = ['name' => 'updated'];
    $this->repository->shouldReceive('find')->with(1)->andReturn($this->model);
    $this->repository->shouldReceive('setModelAttributes')->with($this->model, $attributes);
    $this->repository->shouldReceive('fireSystemEvent')->with('api.repository.beforeUpdate', [$this->model, $attributes]);
    $this->repository->shouldReceive('fireSystemEvent')->with('api.repository.afterUpdate', [$this->model, true]);

    DB::shouldReceive('transaction')->andReturnUsing(function($callback) {
        $callback();
    });

    $result = $this->repository->update(1, $attributes);

    expect($result)->toBe($this->model);
});

it('deletes a record by id', function() {
    $this->repository->shouldReceive('find')->with(1)->andReturn($this->model);
    $this->model->shouldReceive('delete')->andReturn(true);
    $this->repository->shouldReceive('fireSystemEvent')->with('api.repository.afterDelete', [$this->model, true]);

    $result = $this->repository->delete(1);

    expect($result)->toBe($this->model);
});
