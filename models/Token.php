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
class Token extends Model
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
    public $table = 'personal_access_tokens';

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
        'meta.actions.*' => 'string',
    ];

    public static function getModelOptions()
    {
	    
	    echo 'here';
	    exit();
	    
	    
        return self::make()->listGlobalModels();
    }

    public function listGlobalModels()
    {
	    echo 'here';
	    exit();


        return $this->modelListCache = $result;
    }

    public static function getResources()
    {
	    echo 'here';
	    exit();
	    
        return self::all()->keyBy('endpoint')->all();
    }

    /**
     * Returns a list of all api resources.
     * @return array Array keys are endpoints.
     */
    public static function listResources()
    {
	    echo 'here';
	    exit();
        return $user->tokens;
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