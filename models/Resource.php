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
        'meta' => 'array'
    ];

    protected $permalinkable = [
        'endpoint' => [
            'source' => 'name',
        ],
    ];

    public $purgeable = ['transformer_content'];

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

    public function getTransformerContentAttribute($value)
    {
        if (!$this->transformer)
            return null;

        return ApiManager::instance()->getTransformer($this->transformer);
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
                // Ignore invalid plugins and models
            }
        }

        return $this->modelListCache = $result;
    }

    public function afterCreate()
    {
        if (!$this->is_custom)
            return;

        list($controller, $transformer) = ApiManager::instance()->buildResource(
            $this->name, $this->model, $this->meta
        );

        $this->controller = $controller;
        $this->transformer = $transformer;
        $this->save();
    }

    public function afterDelete()
    {
        if (!$this->is_custom)
            return;

        // Delete the resource controller
        ApiManager::instance()->deleteResource($this->name);
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
        $resources = (new static())->listRegisteredResources();
        $dbResources = self::lists('is_custom', 'endpoint')->toArray();
        $newResources = array_diff_key($resources, $dbResources);

        // Clean up non-customized api resources
        foreach ($dbResources as $endpoint => $isCustom) {
            if ($isCustom)
                continue;

            if (!array_key_exists($endpoint, $resources))
                self::whereName($endpoint)->delete();
        }

        // Create new resources
        foreach ($newResources as $endpoint => $definition) {
            $model = self::make();
            $model->endpoint = $endpoint;
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
}