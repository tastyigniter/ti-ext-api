<?php

namespace Igniter\Api\Classes;

use Main\Classes\MainController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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
     * @var string Ability required to view this page.
     * ex. orders.*
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
            if (!$this->checkActionSecurity($action))
                throw new BadRequestHttpException(lang('igniter.api::default.alert_auth_failed'));

            $this->setToken($this->manager->currentAccessToken());

            // Check that the token has ability to perform this action
            if ($this->requiredAbilities AND !$this->manager->currentAccessTokenCan($this->requiredAbilities)) {
                throw new BadRequestHttpException(lang('igniter.api::default.alert_token_restricted'));
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

    public function checkActionSecurity($action)
    {
        $allowedGroup = array_get($this->allowedActions, $action, 'all');
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