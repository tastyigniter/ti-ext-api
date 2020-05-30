<?php

namespace Igniter\Api\Controllers;

use AdminMenu;

/**
 * API Tokens Admin Controller
 */
class Tokens extends \Admin\Classes\AdminController
{
    public $implement = [
        'Admin\Actions\ListController',
    ];

    public $listConfig = [
        'list' => [
            'model' => 'Igniter\Api\Models\Token',
            'title' => 'igniter.api::default.text_tokens_title',
            'emptyMessage' => 'lang:admin::lang.list.text_empty',
            'defaultSort' => ['id', 'DESC'],
            'configFile' => 'token',
        ],
    ];

    protected $requiredPermissions = 'Igniter.Api.*';

    public function __construct()
    {
        parent::__construct();

        AdminMenu::setContext('resources', 'tools');
    }
}
