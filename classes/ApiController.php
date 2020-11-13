<?php

namespace Igniter\Api\Classes;

use Igniter\Api\Traits\AuthorizesRequest;
use Igniter\Api\Traits\CreatesResponse;
use Illuminate\Contracts\Support\Responsable;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use System\Classes\BaseController;

class ApiController extends BaseController
{
    use AuthorizesRequest;
    use CreatesResponse;

    public $implement = ['Igniter.Api.Actions.RestController'];

    public $restConfig = [];

    public $allowedActions = [];

    /**
     * @var array Token abilities required to access methods on this controller.
     * ex. ['orders.*']
     */
    protected $requiredAbilities;

    public function __construct()
    {
        parent::__construct();
    }

    public function getAbilities()
    {
        return $this->requiredAbilities;
    }

    public function callAction($action, $parameters = [])
    {
        $this->action = $action;

        if (!$this->checkAction($action))
            $this->response()->errorNotFound();

        if ($this->token())
            $this->authorizeToken();

        // Execute the action
        $response = call_user_func_array([$this, $action], $parameters);

        return $this->isResponsable($response)
            ? $response : $this->response()->array($response);
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

    protected function authorizeToken()
    {
        $abilities = $this->getAbilities();

        if ($abilities AND !$this->tokenCan($abilities))
            throw new AccessDeniedHttpException(lang('igniter.api::default.alert_token_restricted'));
    }

    protected function isResponsable($response)
    {
        return $response instanceof Response OR $response instanceof Responsable;
    }
}
