<?php

namespace Igniter\Api\Actions;

use Admin\Traits\FormModelWidget;
use Igniter\Api\Classes\ApiManager;
use Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use System\Classes\ControllerAction;

/**
 * Rest Controller Action
 * @package Iginter\Api
 */
class RestController extends ControllerAction
{
    use FormModelWidget;

    /**
     * @var \Igniter\Api\Classes\ApiController|self The child controller that implements the action.
     */
    protected $controller;

    /**
     * @var \Model The initialized model used by the rest controller.
     */
    protected $model;

    /**
     * @var String The prefix for verb methods.
     */
    protected $prefix = '';

    /**
     * {@inheritDoc}
     */
    protected $requiredProperties = ['restConfig'];

    /**
     * @var array Configuration values that must exist when applying the primary config file.
     * - model: Class name for the model
     */
    protected $requiredConfig = ['model', 'actions'];

    /**
     * RestController constructor
     * @param \Main\Classes\MainController $controller
     */
    public function __construct($controller)
    {
        parent::__construct($controller);
        $this->controller = $controller;

        $this->mergeResourceConfigWith(
            $controller->restConfig, $this->requiredConfig
        );

        $this->allowedActions(
            array_get($this->config, 'only', []), array_get($this->config, 'authorization', [])
        );

        $this->controller->transformer = array_get($this->config, 'transformer');
    }

    /**
     * Display the records.
     *
     * @return mixed
     */
    public function index()
    {
        $options = array_merge($this->getActionOptions(), Request::all());
        $transformer = $this->getConfig('transformer');

        $model = $this->controller->restCreateModelObject();
        $model = $this->controller->restExtendModel($model) ?: $model;

        $query = $model->newQuery();
        $this->controller->restExtendQueryBefore($query);

        $this->applyRelationsScope($query);

        $this->controller->restExtendQuery($query);

        if (method_exists($model, 'scopeListFrontEnd')) {
            $result = $query->listFrontEnd($options);
        }
        else {
            $page = array_get($options, 'page', Request::input('page', 1));
            $pageSize = array_get($options, 'pageLimit', 5);
            $result = $query->paginate($pageSize, $page);
        }

        return $this->controller->response()->paginator($result, $transformer);
    }

    /**
     * Store a newly created record using post data.
     *
     * @return mixed
     */
    public function store()
    {
        $data = Request::all();

        $transformer = $this->getConfig('transformer');

        $model = $this->controller->restCreateModelObject();
        $model = $this->controller->restExtendModel($model) ?: $model;

        $this->restBeforeSave($model);
        $this->restBeforeCreate($model);

        $modelsToSave = $this->prepareModelsToSave($model, $data);
        foreach ($modelsToSave as $modelToSave) {
            $modelToSave->save();
        }

        $this->restAfterSave($model);
        $this->restAfterCreate($model);

        return $this->controller->response()->created($model, $transformer);
    }

    /**
     * Display the specified record.
     *
     * @param int $recordId
     * @return mixed
     */
    public function show($recordId)
    {
        $transformer = $this->getConfig('transformer');
        $model = $this->controller->restFindModelObject($recordId);

        return $this->controller->response()->resource($model, $transformer);
    }

    /**
     * Update the specified record in using post data.
     *
     * @param int $recordId
     * @return mixed
     */
    public function update($recordId)
    {
        $data = Request::all();

        $transformer = $this->getConfig('transformer');

        $model = $this->controller->restFindModelObject($recordId);

        $this->restBeforeSave($model);
        $this->restBeforeUpdate($model);

        $modelsToSave = $this->prepareModelsToSave($model, $data);
        foreach ($modelsToSave as $modelToSave) {
            $modelToSave->save();
        }

        $this->restAfterSave($model);
        $this->restAfterUpdate($model);

        return $this->controller->response()->resource($model, $transformer);
    }

    /**
     * Remove the specified record.
     *
     * @param int $recordId
     * @return mixed
     */
    public function destroy($recordId)
    {
        $model = $this->controller->restFindModelObject($recordId);
        $model->delete();

        $this->restAfterDelete($model);

        return $this->controller->response()->noContent();
    }

    public function getActionOptions()
    {
        return array_get($this->getConfig('actions'), $this->controller->getAction(), []);
    }

    /**
     * Finds a Model record by its primary identifier, used by show, update actions.
     * This logic can be changed by overriding it in the rest controller.
     * @param string $recordId
     * @return \Model
     */
    public function restFindModelObject($recordId)
    {
        if (!strlen($recordId)) {
            throw new HttpException(404, 'Record ID has not been specified.');
        }

        $model = $this->controller->restCreateModelObject();

        /*
         * Prepare query and find model record
         */
        $query = $model->newQuery();

        $this->applyRelationsScope($query);

        $this->controller->restExtendQuery($query);
        $result = $query->find($recordId);

        if (!$result) {
            throw new HttpException(404,
                sprintf('Record with an ID of %u could not be found.', $recordId)
            );
        }

        $result = $this->controller->restExtendModel($result) ?: $result;

        return $result;
    }

    /**
     * Run logic before the store or update resource operation
     * by overriding it in the controller.
     * @param \Model $model
     * @return void
     */
    public function restBeforeSave($model)
    {
    }

    /**
     * Run logic before the store resource operation
     * by overriding it in the controller.
     * @param \Model $model
     * @return void
     */
    public function restBeforeCreate($model)
    {
    }

    /**
     * Run logic before the update resource operation
     * by overriding it in the controller.
     * @param \Model $model
     * @return void
     */
    public function restBeforeUpdate($model)
    {
    }

    /**
     * Run logic after the store or update resource operation
     * by overriding it in the controller.
     * @param \Model $model
     * @return void
     */
    public function restAfterSave($model)
    {
    }

    /**
     * Run logic after the store resource operation
     * by overriding it in the controller.
     * @param \Model $model
     * @return void
     */
    public function restAfterCreate($model)
    {
    }

    /**
     * Run logic after the update resource operation
     * by overriding it in the controller.
     * @param \Model $model
     * @return void
     */
    public function restAfterUpdate($model)
    {
    }

    /**
     * Run logic after the delete resource operation
     * by overriding it in the controller.
     * @param \Model $model
     * @return void
     */
    public function restAfterDelete($model)
    {
    }

    /**
     * Creates a new instance of the model. This logic can be changed
     * by overriding it in the rest controller.
     * @return \Model
     */
    public function restCreateModelObject()
    {
        return $this->createModel();
    }

    /**
     * Extend supplied model, the model can
     * be altered by overriding it in the controller.
     * @param \Model $model
     * @return \Model
     */
    public function restExtendModel($model)
    {
    }

    /**
     * Extend supplied model query, the model query can
     * be altered by overriding it in the controller.
     * @param \Igniter\Flame\Database\Builder $query
     * @return void
     */
    public function restExtendQueryBefore($query)
    {
    }

    /**
     * Extend supplied model query, the model query can
     * be altered by overriding it in the controller.
     * @param \Igniter\Flame\Database\Builder $query
     * @return void
     */
    public function restExtendQuery($query)
    {
    }

    /**
     * Internal method, prepare the model object
     * @return \Model
     */
    protected function createModel()
    {
        $class = $this->config['model'];

        return new $class();
    }

    protected function mergeResourceConfigWith(array $restConfig, array $requiredConfig)
    {
        $this->setConfig(array_merge(
            $restConfig, ApiManager::instance()->getCurrentResource()
        ), $requiredConfig);
    }

    protected function allowedActions($allowedActions, $authActions)
    {
        $result = [];
        foreach ($allowedActions as $action) {
            $result[$action] = array_get($authActions, $action, 'admin');
        }

        $this->controller->allowedActions = array_merge(
            $this->controller->allowedActions, $result
        );
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     */
    protected function applyRelationsScope($query)
    {
        if (!$transformer = $this->getConfig('transformer'))
            return;

        $transformerObj = new $transformer();

        $includes = method_exists($transformerObj, 'getIncludes')
            ? $transformerObj->getIncludes() : [];

        $query->with($includes);
    }
}
