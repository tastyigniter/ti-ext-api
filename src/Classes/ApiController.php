<?php

namespace Igniter\Api\Classes;

use Igniter\Admin\Traits\ValidatesForm;
use Igniter\Api\Traits\AuthorizesRequest;
use Igniter\Api\Traits\CreatesResponse;
use Igniter\Flame\Support\Extendable;
use Igniter\Flame\Traits\EventEmitter;
use Illuminate\Contracts\Support\Responsable;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ApiController extends Extendable
{
    use AuthorizesRequest;
    use CreatesResponse;
    use EventEmitter;
    use ValidatesForm;

    public array $allowedActions = [];

    /**
     * @var array Default actions which cannot be called as actions.
     */
    public $hiddenActions = [
        'checkAction',
        'execPageAction',
        'handleError',
    ];

    /**
     * @var array Token abilities required to access methods on this controller.
     * ex. ['orders.*']
     */
    protected $requiredAbilities;

    /**
     * @var int Response status code
     */
    protected $statusCode = 200;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->extendableConstruct();

        $this->fireSystemEvent('api.controller.beforeConstructor', [$this]);
    }

    public function getAbilities()
    {
        return $this->requiredAbilities;
    }

    public function callAction($action, $parameters = [])
    {
        $this->action = $action;

        if (!$this->checkAction($action)) {
            $this->response()->errorNotFound();
        }

        if ($this->token()) {
            $this->authorizeToken();
        }

        // Execute the action
        $response = call_user_func_array([$this, $action], array_values($parameters));

        return $this->isResponsable($response)
            ? $response : response()->json($response);
    }

    public function checkAction($action)
    {
        if (!array_key_exists($action, $this->allowedActions)) {
            return false;
        }

        if (!$methodExists = $this->methodExists($action)) {
            return false;
        }

        if (method_exists($this, $action)) {
            $methodInfo = new \ReflectionMethod($this, $action);

            return $methodInfo->isPublic();
        }

        return $methodExists;
    }

    public function setStatusCode($code)
    {
        $this->statusCode = $code;
    }

    protected function authorizeToken()
    {
        if (!$ability = $this->getAbilities()) {
            return;
        }

        if (is_array($ability)) {
            $ability = implode(',', $ability);
        }

        if (!$this->tokenCan($ability)) {
            throw new AccessDeniedHttpException(lang('igniter.api::default.alert_token_restricted'));
        }
    }

    protected function isResponsable($response)
    {
        return $response instanceof Response || $response instanceof Responsable;
    }
}
