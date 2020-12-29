<?php

namespace Igniter\Api\Models;

use Exception;
use Igniter\Flame\Database\Model;
use Igniter\Flame\Database\Traits\HasPermalink;
use Igniter\Flame\Database\Traits\Validation;
use Igniter\Flame\Mail\Markdown;
use Igniter\Flame\Support\Facades\File;
use System\Classes\ExtensionManager;

/**
 * Resource Model
 */
class Resource extends Model
{
    use HasPermalink;
    use Validation;

    /**
     * @var array A cache of api resources.
     */
    protected static $resourceCache = [];

    /**
     * @var array Cache of registration callbacks.
     */
    protected static $callbacks = [];

    protected static $registeredResources;

    protected static $defaultAuthDefinition = [
        'index' => 'all',
        'show' => 'all',
        'store' => 'admin',
        'update' => 'admin',
        'destroy' => 'admin',
    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'igniter_api_resources';

    /**
     * @var array fillable fields
     */
    protected $fillable = ['name', 'description', 'endpoint', 'model', 'meta'];

    protected $casts = [
        'meta' => 'array',
    ];

    protected $permalinkable = [
        'endpoint' => [
            'source' => 'name',
        ],
    ];

    protected $rules = [
        'name' => 'required|min:2|max:128|alpha_dash',
        'description' => 'required|min:2|max:255',
        'endpoint' => 'max:255|regex:/^[a-z0-9\-_\/]+$/i|unique:igniter_api_resources,endpoint',
        'controller' => 'required|min:2|max:255',
        'meta' => 'array',
        'meta.actions.*' => 'alpha',
        'meta.authorization.*' => 'alpha',
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
        return sprintf('/%s/%s', config('api.prefix'), $this->endpoint);
    }

    public function renderSetupPartial()
    {
        $registeredResources = (new static())->listRegisteredResources();
        $resources = collect($registeredResources)->keyBy('endpoint')->toArray();
        $extensionCode = array_get($resources, $this->endpoint.'.owner');

        $path = ExtensionManager::instance()->path($extensionCode);
        $docsPath = $path.sprintf('docs/%s.md', $this->endpoint);

        return File::existsInsensitive($docsPath)
            ? Markdown::parseFile($docsPath)->toHtml()
            : 'No documentation provided';
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
        $resources = collect($registeredResources)->keyBy('endpoint')->toArray();
        $dbResources = self::lists('is_custom', 'endpoint')->toArray();
        $newResources = array_diff_key($resources, $dbResources);

        // Clean up non-customized api resources
        foreach ($dbResources as $endpoint => $isCustom) {
            if ($isCustom)
                continue;

            if (!array_key_exists($endpoint, $resources))
                self::where('endpoint', $endpoint)->delete();
        }

        // Create new resources
        foreach ($newResources as $endpoint => $definition) {
            $model = self::make();
            $model->endpoint = array_get($definition, 'endpoint');
            $model->name = array_get($definition, 'name');
            $model->description = array_get($definition, 'description');
            $model->controller = array_get($definition, 'controller');
            /** @var TYPE_NAME $model */
            $model->meta = self::getMetaFromPreset($definition);
            $model->is_custom = FALSE;
            $model->save();
        }
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

    /**
     * @param $definition
     * @return mixed
     */
    protected static function getMetaFromPreset($definition)
    {
        $authorization = array_get($definition, 'authorization', []);

        $result = [];
        foreach ($authorization as $item) {
            $item = explode(':', $item);

            $result[$item[0]] = $item[1];
        }

        $authorization = array_merge(self::$defaultAuthDefinition, $result);

        return [
            'actions' => array_keys($authorization),
            'authorization' => $authorization,
        ];
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
            $this->registerResources($resources, $extensionCode);
        }
    }

    /**
     * Registers the api resources.
     * @param array $definitions
     */
    public function registerResources(array $definitions, string $owner = null)
    {
        $defaultDefinitions = [
            'name' => null,
            'description' => null,
            'controller' => null,
            'endpoint' => null,
            'owner' => null,
        ];

        foreach ($definitions as $endpoint => $definition) {
            if (!is_array($definition))
                $definition = ['controller' => $definition];

            $definition['endpoint'] = $endpoint;
            $definition['owner'] = $owner;

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
