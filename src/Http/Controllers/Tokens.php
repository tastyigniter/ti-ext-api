<?php

declare(strict_types=1);

namespace Igniter\Api\Http\Controllers;

use Igniter\Admin\Facades\AdminMenu;

/**
 * API Tokens Admin Controller
 */
class Tokens extends \Igniter\Admin\Classes\AdminController
{
    public array $implement = [
        \Igniter\Admin\Http\Actions\ListController::class,
    ];

    public array $listConfig = [
        'list' => [
            'model' => \Igniter\Api\Models\Token::class,
            'title' => 'igniter.api::default.text_tokens_title',
            'emptyMessage' => 'lang:admin::lang.list.text_empty',
            'defaultSort' => ['id', 'DESC'],
            'configFile' => 'token',
            'back' => 'igniter/api/resources',
        ],
    ];

    protected null|string|array $requiredPermissions = ['index' => 'Igniter.Api.*'];

    public function __construct()
    {
        parent::__construct();

        AdminMenu::setContext('resources', 'tools');
    }
}
