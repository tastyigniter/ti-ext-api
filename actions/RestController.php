<?php

namespace Igniter\Api\Actions;

use Admin\Traits\FormModelWidget;
use Igniter\Api\Classes\AbstractRepository;
use Igniter\Api\Classes\ApiRequest;
use Igniter\Api\Traits\RestExtendable;
use System\Classes\ControllerAction;

/**
 * Rest Controller Action
 */
class RestController extends ControllerAction
{
    use RestExtendable;
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
     * {@inheritdoc}
     */
    protected $requiredProperties = ['restConfig'];

    /**
     * @var array Configuration values that must exist when applying the primary config file.
     * - model: Class name for the model
     */
    protected $requiredConfig = ['actions', 'repository', 'transformer'];

    /**
     * RestController constructor
     * @param \Igniter\Api\Classes\ApiController $controller
     */
    public function __construct($controller)
    {
        parent::__construct($controller);
        $this->controller = $controller;

        $this->setConfig($controller->restConfig, $this->requiredConfig);

        $this->mergeAllowedActions();
    }

    /**
     * Display the records.
     *
     * @return mixed
     */
    public function index()
    {
        $request = $this->resolveFormRequest();

        $repository = $this->makeRepository('query', $request);

        $options = array_merge($this->getActionOptions(), $request->query());
        $result = $repository->findAll($options);

        return $this->controller->response()->paginator(
            $result, $this->createTransformer(), [
                'key' => $this->getConfig('resourceKey', strtolower(class_basename($this->controller))),
            ]
        );
    }

    /**
     * Store a newly created record using post data.
     *
     * @return mixed
     */
    public function store()
    {
        $request = $this->resolveFormRequest();

        $repository = $this->makeRepository('create', $request);

        $result = $repository->create($request->validated());

        return $this->controller->response()->created(
            $result, $this->createTransformer(), null, [
                'key' => $this->getConfig('resourceKey', strtolower(class_basename($this->controller))),
            ]
        );
    }

    /**
     * Display the specified record.
     *
     * @param int $recordId
     * @return mixed
     */
    public function show($recordId)
    {
        $request = $this->resolveFormRequest();

        $repository = $this->makeRepository('query', $request);

        $result = $repository->find($recordId);

        return $this->controller->response()->resource(
            $result, $this->createTransformer(), [
                'key' => $this->getConfig('resourceKey', strtolower(class_basename($this->controller))),
            ]
        );
    }

    /**
     * Update the specified record in using post data.
     *
     * @param int $recordId
     * @return mixed
     */
    public function update($recordId)
    {
        $request = $this->resolveFormRequest();

        $repository = $this->makeRepository('update', $request);

        $result = $repository->update($recordId, $request->validated());

        return $this->controller->response()->resource(
            $result, $this->createTransformer(), [
                'key' => $this->getConfig('resourceKey', strtolower(class_basename($this->controller))),
            ]
        );
    }

    /**
     * Remove the specified record.
     *
     * @param int $recordId
     * @return mixed
     * @throws \Exception
     */
    public function destroy($recordId)
    {
        $request = $this->resolveFormRequest();

        $repository = $this->makeRepository('delete', $request);

        $repository->delete($recordId);

        return $this->controller->response()->noContent();
    }

    public function getActionOptions()
    {
        return array_get($this->getConfig('actions'), $this->controller->getAction(), []);
    }

    //
    //
    //

    protected function mergeAllowedActions()
    {
        $actions = array_get($this->config, 'actions', []);

        $this->controller->allowedActions = array_merge(
            $this->controller->allowedActions, $actions
        );
    }

    protected function createTransformer()
    {
        $transformerClass = $this->getConfig('transformer');

        return new $transformerClass;
    }

    /**
     * @return \Igniter\Api\Classes\ApiRequest
     */
    protected function resolveFormRequest()
    {
        return app()->make($this->getConfig('request', ApiRequest::class));
    }

    /**
     * @param string|null $context
     * @param mixed $request
     * @return \Igniter\Api\Classes\AbstractRepository
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function makeRepository($context, $request = null)
    {
        $repository = app()->make($this->getConfig('repository'));

        $repository->setContainer(app());

        $methodName = studly_case('bind_'.$context.'_events');
        if (method_exists($this, $methodName))
            $this->{$methodName}($repository);

        return $repository;
    }

    protected function bindQueryEvents(AbstractRepository $repository): void
    {
        $repository->bindEvent('repository.extendQuery', function ($query) {
            $this->controller->restExtendQuery($query);
        });
    }

    protected function bindCreateEvents(AbstractRepository $repository): void
    {
        $repository->bindEvent('repository.beforeCreate', function ($model) {
            $this->controller->restBeforeSave($model);
            $this->controller->restBeforeCreate($model);
        });

        $repository->bindEvent('repository.afterCreate', function ($model) {
            $this->controller->restAfterSave($model);
            $this->controller->restAfterCreate($model);
        });
    }

    protected function bindUpdateEvents(AbstractRepository $repository): void
    {
        $repository->bindEvent('repository.beforeUpdate', function ($model) {
            $this->controller->restBeforeSave($model);
            $this->controller->restAfterUpdate($model);
        });

        $repository->bindEvent('repository.afterUpdate', function ($model) {
            $this->controller->restAfterSave($model);
            $this->controller->restAfterUpdate($model);
        });
    }

    protected function bindDeleteEvents(AbstractRepository $repository): void
    {
        $repository->bindEvent('repository.afterDelete', function ($model) {
            $this->controller->restAfterDelete($model);
        });
    }
}
