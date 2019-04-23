<?php

namespace Igniter\Api\Classes;

use Main\Classes\MainController;

class ApiController extends MainController
{
    public $implement = ['Igniter.Api.Actions.RestController'];

    public $restConfig = [];

    public $allowedActions = [];

    public static function getAfterFilters()
    {
        return [];
    }

    public static function getBeforeFilters()
    {
        return [];
    }

    public static function getMiddleware()
    {
        return [];
    }

    public function callAction($action, $parameters = [])
    {
        $this->action = $action;

        if (!$this->checkAction($action))
            return $this->response()->errorNotFound();

        // Execute the action
        return call_user_func_array([$this, $action], $parameters);
    }

    public function checkAction($action)
    {
        if (!array_key_exists($action, $this->allowedActions))
            return FALSE;

        if (!$methodExists = $this->methodExists($action))
            return FALSE;

        if ($ownMethod = method_exists($this, $action)) {
            $methodInfo = new \ReflectionMethod($this, $action);

            return $methodInfo->isPublic();
        }

        return $methodExists;
    }

    public function response()
    {
        return app(ResponseFactory::class);
    }
}