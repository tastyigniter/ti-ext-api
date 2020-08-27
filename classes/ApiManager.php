<?php

namespace Igniter\Api\Classes;

use File;
use Igniter\Api\Models\Resource;
use Igniter\Api\Models\Token;
use Igniter\Flame\Traits\Singleton;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as RequestFacade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\Sanctum;
use Laravel\Sanctum\TransientToken;

class ApiManager
{
    use Singleton;

    protected $baseUri = 'api';

    protected $namespace = '\\Igniter\\Api\\Resources';

    protected $resourcesPath;

    protected $resourcesCache;

    /**
     * The access token the user is using for the current request.
     *
     * @var \Laravel\Sanctum\Contracts\HasAbilities
     */
    protected $accessToken;

    public function initialize()
    {
        $this->resourcesPath = storage_path('framework/api-resources.php');
    }

    public function getBaseEndpoint($endpoint = null)
    {
        $prefix = config('api.prefix');

        $base = $this->baseUri.($prefix ? '/'.$prefix : '');

        return is_null($endpoint) ? $base : $base.'/'.$endpoint;
    }

    public function getResources()
    {
        if ($this->resourcesCache)
            return $this->resourcesCache;

        $resources = [];
        if (File::isFile($this->resourcesPath))
            $resources = File::getRequire($this->resourcesPath);

        if (!is_array($resources))
            $resources = [];

        return $this->resourcesCache = $this->makeResources($resources);
    }

    public function getCurrentResource()
    {
        $this->currentResourceName = Str::before(Str::after(Route::currentRouteName(), 'api.'), '.');

        return array_get($this->getResources(), $this->currentResourceName, []);
    }

    public function getCurrentAction()
    {
        return Str::afterLast(Route::currentRouteAction(), '@');
    }

    public function buildResource($name, $model, $meta = [])
    {
        $controller = $this->parseName($name);
        $singularController = str_singular($controller);

        \Artisan::call('create:apiresource', [
            'extension' => 'Igniter.Api',
            'controller' => $controller,
            '--model' => $model,
            '--meta' => $meta,
        ]);

        if (!class_exists($controllerName = $this->namespace."\\{$controller}"))
            return [null, null];

        return [$controllerName, $this->namespace."\\Transformers\\{$singularController}Transformer"];
    }

    public function writeResources(array $resources)
    {
        $content = [];
        foreach ($resources as $endpoint => $resource) {
            $content[$endpoint] = [
                'controller' => $resource->controller,
                'only' => array_get($resource->meta, 'actions', []),
                'middleware' => array_get($resource->meta, 'middleware', ['api']),
                'authorization' => array_get($resource->meta, 'authorization', []),
            ];
        }

        File::put($this->resourcesPath, '<?php return '.var_export($content, TRUE).';');
    }

    protected function makeResources(array $resources)
    {
        $defaults = [
            'controller' => 'Igniter\Api\Classes\ApiController',
            'only' => ['index', 'store', 'show', 'update', 'destroy'],
            'middleware' => ['api'],
        ];

        $defaults = array_merge($defaults, array_only(Resource::$defaultMetaDefinition, ['authorization']));

        $result = [];
        foreach ($resources as $endpoint => $resource) {
            $result[$endpoint] = array_merge($defaults, $resource);
        }

        return $result;
    }

    protected function parseName($name)
    {
        return studly_case(preg_replace('/[0-9]+/', '', $name));
    }

    protected function getClassPath($class)
    {
        $path = trim(str_replace('\\', '/', $class), '/');

        return extension_path(strtolower(dirname($path)).'/'.basename($path).'.php');
    }

    //
    // Access Tokens
    //

    public function checkToken()
    {
        if (!is_null($this->accessToken))
            return $this->accessToken;

        return $this->authenticateToken(RequestFacade::bearerToken());
    }

    public function authenticateToken($token)
    {
        if ($user = app('auth')->user() AND $this->supportsTokens($user)) {
            $this->setAccessToken($accessToken = (new TransientToken));

            return $accessToken;
        }

        if ($token) {
            if (!$accessToken = $this->findToken($token))
                return null;

            $expiration = config('sanctum.expiration');
            if ($expiration AND $accessToken->created_at->lte(now()->subMinutes($expiration)))
                return null;

            $user = $accessToken->tokenable;

            if (!$this->supportsTokens($user))
                return null;

            $this->setAccessToken(
                tap($accessToken->forceFill(['last_used_at' => now()]))->save()
            );

            return $accessToken;
        }
    }

    /**
     * Get the access token currently associated with the user.
     *
     * @return \Laravel\Sanctum\Contracts\HasAbilities
     */
    public function currentAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Determine if the current API token has a given scope.
     *
     * @param string $ability
     * @return bool
     */
    public function currentAccessTokenCan($ability)
    {
        return $this->accessToken ? $this->accessToken->can($ability) : FALSE;
    }

    /**
     * Determine if the current API token has admin access
     *
     * @return bool
     */
    public function currentAccessTokenIsAdmin()
    {
        $token = $this->currentAccessToken();

        return $token AND $token->tokenable_type == 'admin';
    }

    /**
     * Set the current access token for the user.
     *
     * @param \Laravel\Sanctum\Contracts\HasAbilities $accessToken
     * @return $this
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    /**
     * Find the token instance matching the given token.
     *
     * @param string $token
     * @return \Laravel\Sanctum\PersonalAccessToken
     */
    public function findToken($token)
    {
        $model = Sanctum::$personalAccessTokenModel;

        return $model::findToken($token);
    }

    public static function createToken(Request $request, bool $forAdmin = FALSE)
    {
        $loginFieldName = $forAdmin ? 'username' : 'email';

        $request->validate([
            $loginFieldName => 'required',
            'password' => 'required',
            'device_name' => 'required',
            'abilities' => 'array',
        ]);

        $credentials = [
            $loginFieldName => $request->$loginFieldName,
            'password' => $request->password,
        ];

        $auth = app($forAdmin ? 'admin.auth' : 'auth');
        $user = $auth->getByCredentials($credentials);

        if (!$user OR !$auth->validateCredentials($user, $credentials))
            throw ValidationException::withMessages([
                $loginFieldName => ['The provided credentials are incorrect.'],
            ]);

        $accessToken = Token::createToken($user, $request->device_name, $request->abilities ?? ['*']);

        return $accessToken->plainTextToken;
    }

    /**
     * Determine if the tokenable model supports API tokens.
     *
     * @param mixed $tokenable
     * @return bool
     */
    protected function supportsTokens($tokenable = null)
    {
        if (is_null($tokenable))
            return FALSE;

        if (in_array(HasApiTokens::class, class_uses_recursive(get_class($tokenable))))
            return TRUE;

        return $tokenable instanceof \Igniter\Flame\Auth\Models\User AND $tokenable->hasRelation('tokens');
    }
}
