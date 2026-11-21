<?php

declare(strict_types=1);

namespace Igniter\Api\Models;

use Igniter\Flame\Database\Factories\HasFactory;
use Igniter\Flame\Database\Model;
use Igniter\Flame\Database\Traits\HasPermalink;
use Igniter\Flame\Mail\Markdown;
use Igniter\Flame\Support\Facades\File;
use Igniter\System\Classes\ExtensionManager;
use Illuminate\Support\Str;

/**
 * Resource Model
 *
 * @property int $id
 * @property string $name
 * @property string $endpoint
 * @property string|null $description
 * @property array|null $meta
 * @property bool $is_custom
 * @property-read mixed $base_endpoint
 * @property-read mixed $controller
 * @mixin Model
 */
class Resource extends Model
{
    use HasFactory;
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

    /**
     * Groups an action's authorization can be set to, from most to least restrictive.
     */
    public const array AUTHORIZATION_GROUPS = ['admin', 'customer', 'users', 'guest', 'all'];

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
     * The display label of a resource action, or the action name for custom actions.
     */
    public static function actionLabel(string $action): string
    {
        $label = static::$defaultActionDefinition[$action] ?? null;

        return $label ? lang(Str::after($label, 'lang:')) : $action;
    }

    /**
     * @var list<string> fillable fields
     */
    protected $fillable = ['name', 'description', 'endpoint', 'model', 'meta'];

    protected $casts = [
        'meta' => 'array',
        'is_custom' => 'boolean',
    ];

    protected $permalinkable = [
        'endpoint' => [
            'source' => 'name',
        ],
    ];

    public function getMetaOptions()
    {
        $registeredResource = array_get(static::listRegisteredResources(), $this->endpoint, []);

        return array_get($registeredResource, 'options.names', static::$defaultActionDefinition);
    }

    public function getBaseEndpointAttribute($value): string
    {
        return sprintf('/%s/%s', config('igniter-api.prefix'), $this->endpoint);
    }

    public function getControllerAttribute()
    {
        return array_get(static::listRegisteredResources(), $this->endpoint.'.controller');
    }

    public function getAvailableActions(): array
    {
        $registeredResource = array_get((new static)->listRegisteredResources(), $this->endpoint, []);
        $registeredActions = array_get($registeredResource, 'options.actions', []);
        $dbActions = (array)array_get($this->meta, 'actions', []);

        return array_intersect($dbActions, $registeredActions);
    }

    /**
     * Groups this action's authorization may be set to. An extension restricts them when
     * registering the action as `action:default:allowed,groups`, e.g. `index:admin:admin,customer`.
     *
     * @return list<string>
     */
    public function getAllowedAuthorization(string $action): array
    {
        // An action registered without an allowed list stores [], which means every group is allowed.
        return array_get(array_get($this->getRegisteredOptions(), 'allowed', []), $action) ?: self::AUTHORIZATION_GROUPS;
    }

    /**
     * The group allowed to call this action. A saved value outside the allowed groups falls
     * back to the registered default, so an extension's restriction always holds.
     */
    public function getActionAuthorization(string $action): string
    {
        $default = array_get(array_get($this->getRegisteredOptions(), 'authorization', []), $action, 'all');
        $value = array_get(array_get((array)$this->meta, 'authorization', []), $action, $default);

        return in_array($value, $this->getAllowedAuthorization($action), true) ? $value : $default;
    }

    /**
     * @return array<string, string> Authorization group keyed by each available action.
     */
    public function getAuthorizationMap(): array
    {
        $map = [];
        foreach ($this->getAvailableActions() as $action) {
            $map[$action] = $this->getActionAuthorization($action);
        }

        return $map;
    }

    /**
     * The staff permission an action needs, from the resource's registered `permissions`:
     * unkeyed entries cover every action and an action-keyed entry overrides them, e.g.
     * `['Admin.Menus', 'destroy' => 'Admin.DeleteMenus']`. Several permissions mean any of them.
     */
    public function getActionPermission(string $action): ?string
    {
        $permissions = (array)array_get((array)array_get($this->listRegisteredResources(), $this->endpoint, []), 'permissions', []);
        $permission = array_key_exists($action, $permissions)
            ? (array)$permissions[$action]
            : array_filter($permissions, is_int(...), ARRAY_FILTER_USE_KEY);
        $permission = implode(',', array_filter(array_map(strval(...), $permission)));

        return $permission !== '' ? $permission : null;
    }

    protected function getRegisteredOptions(): array
    {
        return (array)array_get((array)array_get($this->listRegisteredResources(), $this->endpoint, []), 'options', []);
    }

    public function renderSetupPartial()
    {
        $registeredResources = (new static)->listRegisteredResources();
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
     */
    public static function syncAll(): void
    {
        $registeredResources = (new static)->listRegisteredResources();
        $resources = collect($registeredResources)->keyBy('endpoint')->toArray();
        $dbResources = static::lists('is_custom', 'endpoint')->toArray();
        $newResources = array_diff_key($resources, $dbResources);

        // Clean up non-customized api resources
        foreach ($dbResources as $endpoint => $isCustom) {
            if ($isCustom) {
                continue;
            }

            if (!array_key_exists($endpoint, $resources)) {
                static::where('endpoint', $endpoint)->delete();
            }
        }

        // Create new resources
        foreach ($newResources as $definition) {
            $model = new self;
            $model->endpoint = array_get($definition, 'endpoint');
            $model->name = array_get($definition, 'name');
            $model->description = array_get($definition, 'description');
            $model->meta = array_except(array_get($definition, 'options'), ['names', 'allowed']);
            $model->is_custom = false;
            $model->save();
        }
    }

    public static function getResources()
    {
        return static::all()->keyBy('endpoint')->all();
    }

    /**
     * Returns a list of all api resources.
     * @return array Array keys are endpoints.
     */
    public static function listResources(): array|float|int
    {
        $registeredResources = (new static)->listRegisteredResources();
        $dbResources = static::all()->keyBy('endpoint')->all();
        $resources = $registeredResources + $dbResources;
        ksort($resources);

        return $resources;
    }

    protected function processOptions($definition): array
    {
        $actions = array_get($definition, 'actions', []);
        $result = [];
        $allowed = [];
        $names = [];
        foreach ($actions as $action => $name) {
            if (!is_string($action)) {
                $action = $name;
            }

            // Format: action[:default[:allowed,groups]], e.g. `index:admin:admin,customer`
            [$actionName, $default, $allowedGroups] = array_pad(explode(':', (string)$action, 3), 3, '');
            $allowedGroups = array_values(array_intersect(
                array_map(trim(...), explode(',', $allowedGroups)),
                self::AUTHORIZATION_GROUPS,
            ));
            $default = $default !== '' ? $default : 'all';

            $result[$actionName] = ($allowedGroups && !in_array($default, $allowedGroups, true)) ? $allowedGroups[0] : $default;
            $allowed[$actionName] = $allowedGroups;
            $names[$actionName] = array_get(static::$defaultActionDefinition, $actionName, $name);
        }

        return [
            'names' => $names,
            'actions' => array_keys($result),
            'authorization' => $result,
            'allowed' => $allowed,
        ];
    }

    //
    // Registration
    //

    /**
     * Returns a list of the registered resources.
     * @return array
     */
    public function listRegisteredResources()
    {
        if (static::$registeredResources === null) {
            (new static)->loadRegisteredResources();
        }

        return static::$registeredResources;
    }

    /**
     * Loads registered resources from extensions
     */
    public function loadRegisteredResources(): void
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
    public function registerResources(array $definitions, ?string $owner = null): void
    {
        $defaultDefinitions = [
            'name' => null,
            'description' => null,
            'controller' => null,
            'permissions' => [],
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
    public function registerCallback(callable $callback): void
    {
        static::$callbacks[] = $callback;
    }

    public static function clearInternalCache(): void
    {
        static::$registeredResources = null;
        static::$callbacks = [];
        static::$resourceCache = [];
    }
}
