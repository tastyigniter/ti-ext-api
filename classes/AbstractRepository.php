<?php

namespace Igniter\Api\Classes;

use Igniter\Api\Traits\GuardsAttributes;
use Igniter\Api\Traits\HasGlobalScopes;
use Igniter\Flame\Database\Model;
use Igniter\Flame\Exception\SystemException;
use Igniter\Flame\Traits\EventEmitter;
use Illuminate\Contracts\Container\Container;

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

    public function find(int $id, array $attributes = ['*'])
    {
        $model = $this->createModel();
        $query = $this->prepareQuery($model);

        return $query->find($id, $attributes);
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
        }
        else {
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

    public function create(array $attributes)
    {
        $model = $this->createModel();

        $this->fireSystemEvent('api.repository.beforeCreate', [$model, $attributes]);

        $model->fill($attributes);

        $created = $model->save();

        $this->fireSystemEvent('api.repository.afterCreate', [$model, $created]);

        return $model;
    }

    public function update(int $id, array $attributes = [])
    {
        $model = $this->find($id);

        if (!$model) return $model;

        $this->fireSystemEvent('api.repository.beforeUpdate', [$model, $attributes]);

        $model->fill($attributes);

        $updated = $model->save();

        $this->fireSystemEvent('api.repository.afterUpdate', [$model, $updated]);

        return $model;
    }

    public function delete(int $id)
    {
        $model = $this->find($id);

        if (!$model) return $model;

        $deleted = $model->save();

        $this->fireSystemEvent('api.repository.afterDelete', [$model, $deleted]);

        return $model;
    }

    public function getModelClass()
    {
        if (!strlen($modelClass = $this->modelClass))
            throw new SystemException('Missing model on '.get_class($this));

        return $modelClass;
    }

    /**
     * @return \Illuminate\Contracts\Container\Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param \Illuminate\Contracts\Container\Container $container
     * @return \Igniter\Api\Classes\AbstractRepository
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;

        return $this;
    }

    protected function prepareQuery($model)
    {
        $query = $model->newQuery();

        $this->applyScopes($query);

        $this->extendQuery($query);

        $this->fireSystemEvent('api.repository.extendQuery', [$query]);

        return $query;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     * @throws \Igniter\Flame\Exception\SystemException
     */
    protected function createModel()
    {
        $modelClass = $this->getModelClass();

        if (!class_exists('\\'.ltrim($modelClass, '\\'))) {
            throw new SystemException("Class {$modelClass} does NOT exist!");
        }

        $modelClass::extend(function (Model $model) {
            if ($fillable = $this->getFillable())
                $model->fillable($fillable);

            if ($guarded = $this->getGuarded())
                $model->guard($guarded);

            if ($hidden = $this->getHidden())
                $model->setHidden($hidden);

            if ($visible = $this->getVisible())
                $model->setVisible($visible);

            $model->bindEvent('model.getAttribute', [$this, 'getModelAttribute']);
            $model->bindEvent('model.setAttribute', [$this, 'setModelAttribute']);
        });

        $model = new $modelClass;
        if (!$model instanceof Model)
            throw new SystemException("Class {$model} must be an instance of \\Igniter\\Flame\\Database\\Model");

        return $model;
    }

    protected function extendQuery($query)
    {
    }
}
