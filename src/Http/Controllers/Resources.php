<?php

namespace Igniter\Api\Http\Controllers;

use Igniter\Admin\Facades\AdminMenu;

/**
 * API Resources Admin Controller
 */
class Resources extends \Igniter\Admin\Classes\AdminController
{
    public $implement = [
        \Igniter\Admin\Http\Actions\FormController::class,
        \Igniter\Admin\Http\Actions\ListController::class,
    ];

    public $listConfig = [
        'list' => [
            'model' => \Igniter\Api\Models\Resource::class,
            'title' => 'APIs',
            'emptyMessage' => 'lang:admin::lang.list.text_empty',
            'defaultSort' => ['id', 'DESC'],
            'configFile' => 'resource',
        ],
    ];

    public $formConfig = [
        'name' => 'APIs',
        'model' => \Igniter\Api\Models\Resource::class,
        'request' => \Igniter\Api\Requests\ResourceRequest::class,
        'edit' => [
            'title' => 'lang:admin::lang.form.edit_title',
            'redirect' => 'igniter/api/resources/edit/{id}',
            'redirectClose' => 'igniter/api/resources',
        ],
        'preview' => [
            'title' => 'lang:admin::lang.form.preview_title',
            'back' => 'igniter/api/resources',
        ],
        'delete' => [
            'redirect' => 'igniter/api/resources',
        ],
        'configFile' => 'resource',
    ];

    protected $requiredPermissions = 'Igniter.Api.*';

    public function __construct()
    {
        parent::__construct();

        AdminMenu::setContext('resources', 'tools');
    }

    public function index()
    {
        \Igniter\Api\Models\Resource::syncAll();

        $this->asExtension('ListController')->index();
    }
}
