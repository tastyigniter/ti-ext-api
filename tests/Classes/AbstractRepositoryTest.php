<?php

namespace Igniter\Api\Tests\Classes;

use Igniter\Api\Classes\AbstractRepository;
use Igniter\Api\Tests\Fixtures\TestModel;
use Igniter\Cart\Models\Order;
use Igniter\Flame\Database\Builder;
use Igniter\Flame\Database\Model;
use Igniter\Flame\Exception\SystemException;
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

it('finds a record by id and applies location aware scope', function() {
    $model = Mockery::mock(Order::class)->makePartial();
    $this->repository->shouldReceive('createModel')->andReturn($model);
    $this->repository->shouldReceive('prepareQuery')->andReturn($model);
    $model->shouldReceive('find')->with(1, ['*'])->andReturn($model);

    $result = $this->repository->find(1);

    expect($result)->toBe($model);
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

it('returns paginated results when model does not have scopeListFrontEnd', function() {
    $model = Mockery::mock(\Illuminate\Database\Eloquent\Model::class)->makePartial();
    $this->repository->shouldReceive('createModel')->andReturn($model);
    $this->repository->shouldReceive('paginate')->with(5, null)->andReturn('paginated_result');

    $result = $this->repository->findAll(['pageLimit' => 5]);

    expect($result)->toBe('paginated_result');
});

it('returns frontend list results when model has scopeListFrontEnd', function() {
    $builder = Mockery::mock(Builder::class);
    $builder->shouldReceive('listFrontEnd')->with(['option' => 'value'])->andReturn('frontend_list_result');
    $this->repository->shouldReceive('createModel')->andReturn($this->model);
    $this->repository->shouldReceive('prepareQuery')->andReturn($builder);

    $result = $this->repository->findAll(['option' => 'value']);

    expect($result)->toBe('frontend_list_result');
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

it('returns null when model not found for deletion', function() {
    $this->repository->shouldReceive('find')->with(1)->andReturn(null);

    $result = $this->repository->delete(1);

    expect($result)->toBeNull();
});

it('throws exception when model class is missing', function() {
    expect(function() {
        $this->repository->getModelClass();
    })->toThrow(SystemException::class, 'Missing model on');
});

it('throws exception when model class does not exist', function() {
    $repository = new class extends AbstractRepository
    {
        public $modelClass = 'NonExistentModel';
    };

    expect(function() use ($repository) {
        $repository->createModel();
    })->toThrow(SystemException::class, 'Class NonExistentModel does NOT exist!');
});

it('creates model instance when model class exists', function() {
    $repository = new class extends AbstractRepository
    {
        public $modelClass = TestModel::class;
        protected $fillable = ['id', 'name'];
        protected $guarded = ['guarded'];
        protected $hidden = ['hidden'];
        protected $visible = ['visible'];
    };

    $result = $repository->createModel();
    $result->fireEvent('model.afterCreate');

    expect($result)->toBeInstanceOf(Model::class)
        ->and($result->getFillable())->toBe(['id', 'name'])
        ->and($result->getGuarded())->toBe(['guarded'])
        ->and($result->getHidden())->toBe(['hidden'])
        ->and($result->getVisible())->toBe(['visible']);
});

it('sets nested model attributes when attribute is nested', function() {
    $nestedModel = Mockery::mock(\Igniter\Flame\Database\Model::class)->makePartial();
    $nestedModel->shouldReceive('isFillable')->andReturn(true);
    $nestedModel->shouldReceive('hasRelation')->andReturn(false);
    $nestedModel->shouldReceive('getKeyName')->andReturn('id');
    $nestedModel->shouldReceive('getRelationType')->andReturn('belongsTo');
    $nestedModel->shouldReceive('extendableGet')->andReturn(null);
    $nestedModel->shouldReceive('extendableSet')->with('name', 'Nested Name')->once();

    $this->model->shouldReceive('isFillable')->andReturn(true);
    $this->model->shouldReceive('hasRelation')->andReturn(true);
    $this->model->shouldReceive('getRelationType')->andReturn('hasOne');
    $this->model->shouldReceive('extendableGet')->andReturn($nestedModel);
    $this->model->shouldReceive('getKeyName')->andReturn('id');

    $this->repository->shouldReceive('getCustomerAwareColumn')->andReturn('customer_id');
    $this->repository->shouldReceive('setModelAttributes')->passthru();

    $saveData = ['nested' => ['name' => 'Nested Name']];
    $this->repository->setModelAttributes($this->model, $saveData);
});
