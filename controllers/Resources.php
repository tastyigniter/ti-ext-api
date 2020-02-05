<?php

namespace Igniter\Api\Controllers;

use AdminMenu;
use Igniter\Api\Classes\ApiManager;
use Igniter\Api\Models\Resource;

/**
 * API Resources Admin Controller
 */
class Resources extends \Admin\Classes\AdminController
{
    public $implement = [
        'Admin\Actions\FormController',
        'Admin\Actions\ListController',
    ];

    public $listConfig = [
        'list' => [
            'model' => 'Igniter\Api\Models\Resource',
            'title' => 'APIs',
            'emptyMessage' => 'lang:admin::lang.list.text_empty',
            'defaultSort' => ['order_id', 'DESC'],
            'configFile' => 'resource',
        ],
    ];

    public $formConfig = [
        'name' => 'APIs',
        'model' => 'Igniter\Api\Models\Resource',
        'edit' => [
            'title' => 'lang:admin::lang.form.edit_title',
            'redirect' => 'igniter/api/resources/edit/{id}',
            'redirectClose' => 'igniter/api/resources',
        ],
        'preview' => [
            'title' => 'lang:admin::lang.form.preview_title',
            'redirect' => 'igniter/api/resources',
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

    public function formAfterSave($model)
    {
        ApiManager::instance()->writeResources(Resource::getResources());
    }
}
