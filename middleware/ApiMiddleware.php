<?php namespace Igniter\Api\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Route;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ApiMiddleware
{
    /**
     * @var \Igniter\Api\Classes\ApiManager
     */
    protected $apiManager;

    public function __construct()
    {
        $this->apiManager = \Igniter\Api\Classes\ApiManager::instance();
    }

    public function handle(Request $request, \Closure $next)
    {
        $authenticationRequired = TRUE;
        if ($resource = $this->apiManager->getCurrentResource()) {
            $action = Str::afterLast(Route::currentRouteAction(), '@');
            $authenticationRequired = in_array($action, $resource['authorization']);
                        
            $actionMap = [
	            'index' => 'List',
	            'show' => 'View',
	            'store' => 'Create',
	            'update' => 'Update',
	            'destroy' => 'Delete'
            ];
            
            $acceptableAbilities = ['*', studly_case($this->apiManager->currentResourceName).'.*', studly_case($this->apiManager->currentResourceName).'.'.$actionMap[$action]];
                    
            if ($authenticationRequired AND !$this->apiManager->authenticateToken($request->bearerToken(), $acceptableAbilities))
                throw new BadRequestHttpException;
            
        }

        return $next($request);
    }
}