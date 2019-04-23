<?php

namespace Igniter\Api\Actions;

use Admin\Traits\FormModelWidget;
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

        $this->formConfig = $controller->restConfig;
        $this->setConfig($controller->restConfig, $this->requiredConfig);

        $this->controller->allowedActions = array_merge(
            $this->controller->allowedActions, array_get($this->config, 'actions')
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
        $options = $this->getActionOptions();
        $page = array_get($options, 'page', Request::input('page', 1));
        $pageSize = array_get($options, 'pageSize', 5);
        $transformer = $this->getConfig('transformer');
        $relations = $this->getConfig('relations', []);
        if (is_string($relations))
            $relations = explode(',', $relations);

        $model = $this->controller->restCreateModelObject();
        $model = $this->controller->restExtendModel($model) ?: $model;

        $result = $model->with($relations)->paginate($pageSize, $page);

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

        $model = $this->controller->restCreateModelObject();
        $model = $this->controller->restExtendModel($model) ?: $model;

        $modelsToSave = $this->prepareModelsToSave($model, $data);
        foreach ($modelsToSave as $modelToSave) {
            $modelToSave->save();
        }

        return $this->controller->response()->created($model);
    }

    /**
     * Display the specified record.
     *
     * @param  int $recordId
     * @return mixed
     */
    public function show($recordId)
    {
        $options = $this->getActionOptions();
        $transformer = $this->getConfig('transformer');
        $relations = array_get($options, 'relations', []);

        $model = $this->controller->restFindModelObject($recordId);

        // Get relations too
        foreach ($relations as $relation)
            $model->{$relation};

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

        $modelsToSave = $this->prepareModelsToSave($model, $data);
        foreach ($modelsToSave as $modelToSave) {
            $modelToSave->save();
        }

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
        $transformer = $this->getConfig('transformer');

        $model = $this->controller->restFindModelObject($recordId);
        $model->delete();

        return $this->controller->response()->resource($model, $transformer);
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
}
