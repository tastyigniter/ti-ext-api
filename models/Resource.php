<?php

namespace Igniter\Api\Models;

use Exception;
use Igniter\Api\Classes\ApiManager;
use Igniter\Flame\Database\Traits\HasPermalink;
use Igniter\Flame\Database\Traits\Purgeable;
use Igniter\Flame\Database\Traits\Validation;
use Model;
use System\Classes\ExtensionManager;

/**
 * Resource Model
 */
class Resource extends Model
{
    use HasPermalink;
    use Validation;
    use Purgeable;

    /**
     * @var array A cache of api resources.
     */
    protected static $resourceCache = [];

    /**
     * @var array Cache of registration callbacks.
     */
    protected static $callbacks = [];

    protected static $registeredResources;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'igniter_api_resources';

    /**
     * @var array fillable fields
     */
    protected $fillable = ['name', 'description', 'endpoint', 'model', 'meta'];

    public $casts = [
        'meta' => 'array',
    ];

    protected $permalinkable = [
        'endpoint' => [
            'source' => 'name',
        ],
    ];

    protected $purgeable = ['transformer_content'];

    protected $rules = [
        'name' => 'required|min:2|max:128|unique:igniter_api_resources,endpoint|regex:/^[\pL\s\-]+$/u',
        'description' => 'required|min:2|max:255',
        'endpoint' => 'max:255|unique:igniter_api_resources,endpoint',
        'model' => 'required|min:2|max:255',
        'meta' => 'array',
    ];

    public static function getModelOptions()
    {
        return self::make()->listGlobalModels();
    }

    public function listGlobalModels()
    {
        if (!is_null($this->modelListCache))
            return $this->modelListCache;

        $result = [];
        $manager = ExtensionManager::instance();
        $extensions = $manager->getExtensions();
        foreach ($extensions as $code => $extension) {
            try {
                $extensionPath = $manager->path($manager->getNamePath($code));
                $modelNamespace = str_replace('.', '\\', $code).'\\Models\\';

                $models = \File::files($extensionPath.'models');
                foreach ($models as $model) {
                    $fullClassName = $modelNamespace.$model->getBasename('.php');
                    $result[$fullClassName] = $code.' - '.$model->getBasename('.php');
                }
            }
            catch (Exception $ex) {
                // Ignore invalid extensions and models
            }
        }

        return $this->modelListCache = $result;
    }

    public function getBaseEndpointAttribute($value)
    {
        return ApiManager::instance()->getBaseEndpoint($this->endpoint);
    }

    //
    // Manager
    //

    /**
     * Synchronise all resources to the database.
     * @return void
     */
    public static function syncAll()
    {
        $registeredResources = (new static())->listRegisteredResources();
        $resources = collect($registeredResources)->keyBy('controller')->toArray();
        $dbResources = self::lists('is_custom', 'controller')->toArray();
        $newResources = array_diff_key($resources, $dbResources);

        // Clean up non-customized api resources
        foreach ($dbResources as $controller => $isCustom) {
            if ($isCustom)
                continue;

            if (!array_key_exists($controller, $resources))
                self::where('controller', $controller)->delete();
        }

        // Create new resources
        foreach ($newResources as $controller => $definition) {
            $model = self::make();
            $model->endpoint = array_get($definition, 'endpoint');
            $model->name = array_get($definition, 'name');
            $model->model = array_get($definition, 'model');
            $model->controller = array_get($definition, 'controller');
            $model->transformer = array_get($definition, 'transformer');
            $model->description = array_get($definition, 'description');
            $model->meta = array_get($definition, 'meta', []);
            $model->is_custom = FALSE;
            $model->save();
        }

        ApiManager::instance()->writeResources(self::getResources());
    }

    public static function getResources()
    {
        return self::all()->keyBy('endpoint')->all();
    }

    /**
     * Returns a list of all api resources.
     * @return array Array keys are endpoints.
     */
    public static function listResources()
    {
        $registeredResources = self::listRegisteredResources();
        $dbResources = self::all()->keyBy('endpoint')->all();
        $resources = $registeredResources + $dbResources;
        ksort($resources);

        return $resources;
    }

    //
    // Registration
    //

    /**
     * Returns a list of the registered resources.
     * @return array
     */
    public static function listRegisteredResources()
    {
        if (self::$registeredResources === null) {
            (new static)->loadRegisteredResources();
        }

        return self::$registeredResources;
    }

    /**
     * Loads registered resources from extensions
     * @return void
     */
    public function loadRegisteredResources()
    {
        if (!static::$registeredResources) {
            static::$registeredResources = [];
        }

        foreach (static::$callbacks as $callback) {
            $callback($this);
        }

        $registeredResources = ExtensionManager::instance()->getRegistrationMethodValues('registerApiResources');
        foreach ($registeredResources as $extensionCode => $resources) {
            $this->registerResources($resources);
        }
    }

    /**
     * Registers the api resources.
     * @param array $definitions
     */
    public function registerResources(array $definitions)
    {
        $defaultDefinitions = [
            'name' => null,
            'description' => null,
            'controller' => null,
            'transformer' => null,
        ];

        foreach ($definitions as $endpoint => $definition) {
            if (!is_array($definition))
                $definition = ['controller' => $definition];

            $definition['endpoint'] = $endpoint;

            static::$registeredResources[$endpoint] = array_merge($defaultDefinitions, $definition);
        }
    }

    /**
     * Registers a callback function that defines api resources.
     * The callback function should register permissions by calling the manager's
     * registerResources() function. The manager instance is passed to the
     * callback function as an argument. Usage:
     * <pre>
     *   Resource::registerCallback(function($manager){
     *       $manager->registerResources([...]);
     *   });
     * </pre>
     *
     * @param callable $callback A callable function.
     */
    public function registerCallback(callable $callback)
    {
        $this->callbacks[] = $callback;
    }
}