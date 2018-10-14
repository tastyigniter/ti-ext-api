<?php

namespace Igniter\Api\Controllers;

use AdminMenu;
use Igniter\Api\Classes\ApiManager;

/**
 * API Resources Admin Controller
 */
class Resources extends \Admin\Classes\AdminController
{
    public $implement = [
        'Admin\Actions\FormController',
        'Admin\Actions\ListController'
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
        'create' => [
            'title' => 'lang:admin::lang.form.create_title',
            'redirect' => 'igniter/api/resources/edit/{id}',
            'redirectClose' => 'igniter/api/resources',
        ],
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

    protected $requiredPermissions = ['Igniter.Api'];

    public function __construct()
    {
        parent::__construct();

        AdminMenu::setContext('resources', 'tools');
    }

    public function index()
    {
        if ($this->getUser()->hasPermission('Igniter.Api.Manage'))
            \Igniter\Api\Models\Resource::syncAll();

        $this->asExtension('ListController')->index();
    }

    public function formExtendFields($form, $fields)
    {
        if ($form->context != 'create') {
            $field = $form->getField('model');
            $field->disabled = TRUE;

            $field = $form->getField('meta[relations]');
            $field->disabled = TRUE;
        }
    }

    public function formBeforeCreate($model)
    {
        $model->is_custom = TRUE;
    }

    public function formAfterSave($model)
    {
        $resource = post('Resource');
        if (!empty($content = array_get($resource, 'transformer_content'))) {
            $name = array_get($resource, 'name');
            $model->transformer = ApiManager::instance()->writeTransformer(
                $name, $content, $model->transformer
            );

            $model->save();
        }

        ApiManager::instance()->writeResources(\Igniter\Api\Models\Resource::getResources());
    }
}
