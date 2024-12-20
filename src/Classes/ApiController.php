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

    public ?string $action = null;

    public array $allowedActions = [];

    /** Token abilities required to access methods on this controller. ex. ['orders.*'] */
    protected string|array $requiredAbilities = [];

    /** Response status code */
    protected int $statusCode = 200;

    public function __construct()
    {
        $this->extendableConstruct();

        $this->fireSystemEvent('api.controller.beforeConstructor', [$this]);
    }

    public function getAbilities(): string|array
    {
        return $this->requiredAbilities;
    }

    public function callAction(string $action, array $parameters = []): mixed
    {
        $this->action = $action;

        if (!$this->checkAction($action)) {
            return response()->json()->setStatusCode(404);
        }

        if ($this->token()) {
            $this->authorizeToken();
        }

        // Execute the action
        $response = call_user_func_array([$this, $action], array_values($parameters));

        return $this->isResponsable($response)
            ? $response : response()->json($response);
    }

    public function checkAction(string $action): bool
    {
        if (!array_key_exists($action, $this->allowedActions)) {
            return false;
        }

        if (!method_exists($this, $action) && $this->methodExists($action)) {
            return true;
        }

        if (!method_exists($this, $action)) {
            return false;
        }

        return (new \ReflectionMethod($this, $action))->isPublic();
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

    protected function isResponsable(mixed $response): bool
    {
        return $response instanceof Response || $response instanceof Responsable;
    }
}
