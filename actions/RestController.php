<?php

namespace Igniter\Api\Actions;

use Admin\Traits\FormModelWidget;
use Dingo\Api\Exception\ResourceException;
use Igniter\Api\Classes\AbstractRepository;
use Igniter\Api\Traits\RestExtendable;
use Igniter\Flame\Exception\ValidationException;
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
     * @var \Igniter\Flame\Database\Model The initialized model used by the rest controller.
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
        $requestQuery = $this->validateRequest('query');

        $options = array_merge($this->getActionOptions(), $requestQuery);

        $result = $this->makeRepository('query')->findAll($options);

        return $this->controller->response()->paginator(
            $result, $this->createTransformer(), $this->getResponseParameters()
        );
    }

    /**
     * Store a newly created record using post data.
     *
     * @return mixed
     */
    public function store()
    {
        $repository = $this->makeRepository('create');

        $this->model = $repository->createModel();

        $request = $this->validateRequest('all');

        $model = $repository->create($this->model, $request);

        return $this->controller->response()->created(
            $model, $this->createTransformer(), null, $this->getResponseParameters()
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
        $this->validateRequest('query');

        $result = $this->makeRepository('query')->find($recordId);

        return $this->controller->response()->resource(
            $result, $this->createTransformer(), $this->getResponseParameters()
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
        $repository = $this->makeRepository('update');

        $this->model = $repository->find($recordId);

        $request = $this->validateRequest('all');

        $result = $this->makeRepository('update')->update($this->model, $request);

        return $this->controller->response()->resource(
            $result, $this->createTransformer(), $this->getResponseParameters()
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
        $this->makeRepository('delete')->delete($recordId);

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
     * @param string $requestMethod query or all
     * @return array
     * @throws \Dingo\Api\Exception\ValidationHttpException
     */
    protected function validateRequest($requestMethod)
    {
        $requestData = request()->$requestMethod();

        try {
            if ($requestMethod == 'query')
                return $this->controller->restValidateQuery($requestData);

            if ($requestClass = $this->getConfig('request')) {
                app()->resolving($requestClass, function ($request, $app) {
                    if (method_exists($request, 'setController'))
                        $request->setController($this->controller);
                });

                $request = app()->make($requestClass);

                return $request->$requestMethod();
            }

            return $this->controller->restValidate($requestData);
        }
        catch (ValidationException $ex) {
            throw new ResourceException(lang('igniter.api::default.alert_validation_failed'), $ex->getErrors());
        }
    }

    /**
     * @param string|null $context
     * @return \Igniter\Api\Classes\AbstractRepository
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function makeRepository($context)
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

    protected function getResponseParameters(): array
    {
        return [
            'key' => $this->getConfig('resourceKey', strtolower(class_basename($this->controller))),
        ];
    }
}
