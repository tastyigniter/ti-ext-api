<?php

namespace Igniter\Api\Classes;

use Main\Classes\MainController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class ApiController extends MainController
{
    public $implement = ['Igniter.Api.Actions.RestController'];

    public $restConfig = [];

    public $allowedActions = [];

    protected $manager;

    /**
     * @var bool If TRUE, this class requires the token to be authenticated
     * before accessing any method.
     */
    protected $requireAuthentication = TRUE;

    /**
     * @var array Token abilities required to access methods on this controller.
     * ex. ['orders.*']
     */
    protected $requiredAbilities;

    public function __construct($theme = null)
    {
        parent::__construct($theme);

        $this->manager = ApiManager::instance();
    }

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

        // Determine if authentication is required
        if ($this->requireAuthentication) {
            $allowedGroup = array_get($this->allowedActions, $action, 'all');
            if (!$this->checkToken($allowedGroup))
                throw new UnauthorizedHttpException('Bearer', lang('igniter.api::default.alert_auth_failed'));

            if (!$this->checkActionSecurity($allowedGroup))
                throw new AccessDeniedHttpException(lang('igniter.api::default.alert_auth_restricted'));

            if ($token = $this->getToken()) {
                $this->setToken($token);

                // Check that the token has ability to perform this action
                if ($this->requiredAbilities AND !$this->manager->currentAccessTokenCan($this->requiredAbilities)) {
                    throw new AccessDeniedHttpException(lang('igniter.api::default.alert_token_restricted'));
                }
            }
        }

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

    //
    //
    //

    public function setToken($currentToken)
    {
        $this->manager->setAccessToken($currentToken);
    }

    public function getToken()
    {
        return $this->manager->currentAccessToken();
    }

    public function checkToken($allowedGroup)
    {
        $requiresValidToken = in_array($allowedGroup, ['admin', 'customer', 'users']);
        if ($requiresValidToken AND !$this->manager->checkToken())
            return FALSE;

        return TRUE;
    }

    public function checkActionSecurity($allowedGroup)
    {
        $currentToken = $this->manager->checkToken();
        $isAuthenticated = !is_null($currentToken);

        if ($isAuthenticated) {
            if ($allowedGroup == 'guest')
                return FALSE;

            if ($allowedGroup == 'admin' AND !$currentToken->isForAdmin())
                return FALSE;

            if ($allowedGroup == 'customer' AND $currentToken->isForAdmin())
                return FALSE;
        }
        else {
            if (in_array($allowedGroup, ['admin', 'customer', 'users']))
                return FALSE;
        }

        return TRUE;
    }
}