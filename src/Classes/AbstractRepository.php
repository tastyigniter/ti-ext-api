<?php

namespace Igniter\Api\Classes;

use Igniter\Api\Traits\GuardsAttributes;
use Igniter\Api\Traits\HasGlobalScopes;
use Igniter\Flame\Database\Model;
use Igniter\Flame\Exception\SystemException;
use Igniter\Flame\Traits\EventEmitter;
use Igniter\User\Models\Customer;
use Igniter\User\Models\User;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AbstractRepository
{
    use EventEmitter;
    use GuardsAttributes;
    use HasGlobalScopes;

    /**
     * The IoC container instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * The repository model.
     *
     * @var string
     */
    protected $modelClass;

    /**
     * @var array List of prepared models that require saving.
     */
    protected $modelsToSave = [];

    protected static $locationAwareConfig;

    protected static $customerAwareConfig;

    public function find(int $id, array $attributes = ['*'])
    {
        $model = $this->createModel();
        $query = $this->prepareQuery($model);

        throw_unless(
            $record = $query->find($id, $attributes),
            new NotFoundHttpException(sprintf('Record with identifier [%s] not found.', $id))
        );

        return $record;
    }

    public function findBy($attribute, $value, array $attributes = ['*'])
    {
        $model = $this->createModel();
        $query = $this->prepareQuery($model);

        return $query->where($attribute, $value)->first($attributes);
    }

    public function findAll(array $options = [])
    {
        $model = $this->createModel();

        if (method_exists($model, 'scopeListFrontEnd')) {
            $query = $this->prepareQuery($model);
            $result = $query->listFrontEnd($options);
        } else {
            $page = array_get($options, 'page');
            $pageSize = array_get($options, 'pageLimit', 5);
            $result = $this->paginate($pageSize, $page);
        }

        return $result;
    }

    public function paginate($perPage = null, $page = null, $pageName = 'page', $columns = ['*'])
    {
        $model = $this->createModel();
        $query = $this->prepareQuery($model);

        return $query->paginate($perPage, $page, $columns, $pageName);
    }

    public function create(Model $model, array $attributes)
    {
        $this->fireSystemEvent('api.repository.beforeCreate', [$model, $attributes]);

        $this->modelsToSave = [];
        $this->setModelAttributes($model, $attributes);

        $this->setCustomerAwareAttributes($model);

        DB::transaction(function() {
            foreach ($this->modelsToSave as $modelToSave) {
                $modelToSave->save();
            }
        });

        // Reload attributes
        $model->reload();

        $this->fireSystemEvent('api.repository.afterCreate', [$model, true]);

        return $model;
    }

    public function update($id, array $attributes = [])
    {
        $model = is_numeric($id)
            ? $this->find($id) : $id;

        if (!$model) {
            return $model;
        }

        $this->fireSystemEvent('api.repository.beforeUpdate', [$model, $attributes]);

        $this->modelsToSave = [];
        $this->setModelAttributes($model, $attributes);

        DB::transaction(function() {
            foreach ($this->modelsToSave as $modelToSave) {
                $modelToSave->save();
            }
        });

        $this->fireSystemEvent('api.repository.afterUpdate', [$model, true]);

        return $model;
    }

    public function delete(int $id)
    {
        $model = $this->find($id);

        if (!$model) {
            return $model;
        }

        $deleted = $model->delete();

        $this->fireSystemEvent('api.repository.afterDelete', [$model, $deleted]);

        return $model;
    }

    public function getModelClass()
    {
        if (!strlen($modelClass = $this->modelClass)) {
            throw new SystemException('Missing model on '.get_class($this));
        }

        return $modelClass;
    }

    public function createModel(): Model
    {
        $modelClass = $this->getModelClass();

        if (!class_exists('\\'.ltrim($modelClass, '\\'))) {
            throw new SystemException("Class $modelClass does NOT exist!");
        }

        $this->prepareModel($modelClass);

        $model = new $modelClass;
        if (!$model instanceof Model) {
            throw new SystemException("Class $model must be an instance of \\Igniter\\Flame\\Database\\Model");
        }

        return $model;
    }

    /**
     * @return \Illuminate\Contracts\Container\Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return \Igniter\Api\Classes\AbstractRepository
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;

        return $this;
    }

    protected function prepareModel(string $modelClass): void
    {
        $modelClass::extend(function(Model $model) {
            if ($fillable = $this->getFillable()) {
                $model->mergeFillable($fillable);
            }

            if ($guarded = $this->getGuarded()) {
                $model->mergeGuarded($guarded);
            }

            if ($hidden = $this->getHidden()) {
                $model->setHidden($hidden);
            }

            if ($visible = $this->getVisible()) {
                $model->setVisible($visible);
            }

            if ($relationKeys = collect($model->getRelationDefinitions())->collapse()->keys()) {
                $model->makeHidden($relationKeys->toArray());
            }

            $this->extendModel($model);

            $model->bindEvent('model.getAttribute', [$this, 'getModelAttribute']);
            $model->bindEvent('model.setAttribute', [$this, 'setModelAttribute']);

            foreach ([
                'beforeCreate', 'afterCreate',
                'beforeUpdate', 'afterUpdate',
                'beforeSave', 'afterSave',
                'beforeDelete', 'afterDelete',
            ] as $method) {
                $model->bindEvent('model.'.$method, function() use ($model, $method) {
                    if (method_exists($this, $method)) {
                        $this->$method($model);
                    }
                });
            }
        });
    }

    protected function prepareQuery($model)
    {
        $query = $model->newQuery();

        $this->applyScopes($query);

        $this->applyLocationAwareScope($query);

        $this->applyCustomerAwareScope($query);

        $this->extendQuery($query);

        $this->fireSystemEvent('api.repository.extendQuery', [$query]);

        return $query;
    }

    protected function extendQuery($query)
    {
    }

    protected function extendModel($model)
    {
    }

    protected function setModelAttributes($model, $saveData)
    {
        if (!is_array($saveData) || !$model) {
            return;
        }

        $this->modelsToSave[] = $model;

        $singularTypes = ['belongsTo', 'hasOne', 'morphOne'];
        foreach ($saveData as $attribute => $value) {
            if ($attribute === $model->getKeyName() || !$model->isFillable($attribute)) {
                continue;
            }

            if ($attribute === $this->getCustomerAwareColumn() && request()->user() instanceof Customer) {
                continue;
            }

            $isNested = ($attribute == 'pivot' || (
                $model->hasRelation($attribute) &&
                in_array($model->getRelationType($attribute), $singularTypes)
            ));

            if ($isNested && is_array($value) && $model->{$attribute}) {
                $this->setModelAttributes($model->{$attribute}, $value);
            } elseif (!starts_with($attribute, '_')) {
                $model->{$attribute} = $value;
            }
        }
    }

    protected function applyLocationAwareScope($query)
    {
        if (!is_array($config = static::$locationAwareConfig)) {
            return;
        }

        if (!in_array(\Igniter\Local\Models\Concerns\Locationable::class, class_uses($query->getModel()))) {
            return;
        }

        $user = request()->user();
        if (!$user instanceof User) {
            return;
        }

        $ids = $user->locations->where('location_status', true)->pluck('location_id')->all();
        if (empty($ids)) {
            return;
        }

        array_get($config, 'addAbsenceConstraint', true)
            ? $query->whereHasOrDoesntHaveLocation($ids)
            : $query->whereHasLocation($ids);
    }

    protected function applyCustomerAwareScope($query)
    {
        if (!is_array(static::$customerAwareConfig)) {
            return;
        }

        $columnName = $this->getCustomerAwareColumn();
        if ($columnName && $customer = $this->getCustomerAwareUser()) {
            $query->where($columnName, $customer->getKey());
        }
    }

    protected function setCustomerAwareAttributes($model)
    {
        $columnName = $this->getCustomerAwareColumn();
        if ($columnName && $model->getKeyName() !== $columnName && $customer = $this->getCustomerAwareUser()) {
            $model->{$columnName} = $customer->getKey();
        }
    }

    protected function getCustomerAwareColumn()
    {
        return array_get(static::$customerAwareConfig, 'column', 'customer_id');
    }

    protected function getCustomerAwareUser()
    {
        return ($customer = request()->user()) instanceof Customer ? $customer : null;
    }
}
