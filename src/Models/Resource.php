<?php

namespace Igniter\Api\Models;

use Igniter\Flame\Database\Model;
use Igniter\Flame\Database\Traits\HasPermalink;
use Igniter\Flame\Mail\Markdown;
use Igniter\Flame\Support\Facades\File;
use Igniter\System\Classes\ExtensionManager;

/**
 * Resource Model
 */
class Resource extends Model
{
    use HasPermalink;

    /**
     * @var array A cache of api resources.
     */
    protected static $resourceCache = [];

    /**
     * @var array Cache of registration callbacks.
     */
    protected static $callbacks = [];

    protected static $registeredResources;

    protected static $defaultActionDefinition = [
        'index' => 'lang:igniter.api::default.actions.text_index',
        'show' => 'lang:igniter.api::default.actions.text_show',
        'store' => 'lang:igniter.api::default.actions.text_store',
        'update' => 'lang:igniter.api::default.actions.text_update',
        'destroy' => 'lang:igniter.api::default.actions.text_destroy',
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

    public function getMetaOptions()
    {
        $registeredResource = array_get(self::listRegisteredResources(), $this->endpoint, []);

        return array_get($registeredResource, 'options.names', self::$defaultActionDefinition);
    }

    public function getBaseEndpointAttribute($value)
    {
        return sprintf('/%s/%s', config('igniter.api.prefix'), $this->endpoint);
    }

    public function getControllerAttribute()
    {
        return array_get($this->listRegisteredResources(), $this->endpoint.'.controller');
    }

    public function getAvailableActions()
    {
        $registeredResource = array_get(self::listRegisteredResources(), $this->endpoint, []);
        $registeredActions = array_get($registeredResource, 'options.actions', []);
        $dbActions = (array)array_get($this->meta, 'actions', []);

        return array_intersect($dbActions, $registeredActions);
    }

    public function renderSetupPartial()
    {
        $registeredResources = (new static())->listRegisteredResources();
        $resources = collect($registeredResources)->keyBy('endpoint')->toArray();
        $extensionCode = array_get($resources, $this->endpoint.'.owner');

        $path = resolve(ExtensionManager::class)->path($extensionCode);
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
            if ($isCustom) {
                continue;
            }

            if (!array_key_exists($endpoint, $resources)) {
                self::where('endpoint', $endpoint)->delete();
            }
        }

        // Create new resources
        foreach ($newResources as $definition) {
            $model = self::make();
            $model->endpoint = array_get($definition, 'endpoint');
            $model->name = array_get($definition, 'name');
            $model->description = array_get($definition, 'description');
            $model->meta = array_except(array_get($definition, 'options'), 'names');
            $model->is_custom = false;
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

    protected function processOptions($definition)
    {
        $actions = array_get($definition, 'actions', []);

        $result = $names = [];
        foreach ($actions as $action => $name) {
            if (!is_string($action)) {
                $action = $name;
            }

            $action = explode(':', $action, 2);
            $result[$action[0]] = $action[1] ?? 'all';
            $names[$action[0]] = array_get(self::$defaultActionDefinition, $action[0], $name);
        }

        return [
            'names' => $names,
            'actions' => array_keys($result),
            'authorization' => $result,
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

        $registeredResources = resolve(ExtensionManager::class)->getRegistrationMethodValues('registerApiResources');
        foreach ($registeredResources as $extensionCode => $resources) {
            $this->registerResources($resources, $extensionCode);
        }
    }

    /**
     * Registers the api resources.
     */
    public function registerResources(array $definitions, ?string $owner = null)
    {
        $defaultDefinitions = [
            'name' => null,
            'description' => null,
            'controller' => null,
            'endpoint' => null,
            'owner' => null,
            'options' => [],
        ];

        foreach ($definitions as $endpoint => $definition) {
            if (!is_array($definition)) {
                $definition = ['controller' => $definition];
            }

            $definition['endpoint'] = $endpoint;
            $definition['owner'] = $owner;
            $definition['options'] = $this->processOptions($definition);

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
