<?php

namespace Igniter\Api\Http\Controllers;

use Igniter\Admin\Facades\AdminMenu;
use Igniter\Api\Models\Token;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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

    protected null|string|array $requiredPermissions = 'Igniter.Api.*';

    public function __construct()
    {
        parent::__construct();

        AdminMenu::setContext('resources', 'tools');
    }

    public function create(Request $request)
    {
        $request->validate([
            'email' => 'email:filter',
            'password' => 'required',
            'is_admin' => 'boolean',
            'device_name' => 'required|alpha_dash',
            'abilities.*' => 'regex:/^[a-zA-Z-_\*]+$/',
        ]);

        $forAdmin = $request->get('is_admin', false);

        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        $auth = app($forAdmin ? 'admin.auth' : 'main.auth');
        $user = $auth->getByCredentials($credentials);

        if (!$user || !$auth->validateCredentials($user, $credentials)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (!$user->is_activated) {
            throw ValidationException::withMessages([
                'email' => ['Inactive user account'],
            ]);
        }

        $token = Token::createToken($user, $request->device_name, $request->abilities ?? ['*']);

        return response()->json([
            'status_code' => 201,
            'token' => $token->plainTextToken,
        ])->setStatusCode(201);
    }
}
