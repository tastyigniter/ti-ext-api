<?php

namespace Igniter\Api\Controllers;

use Admin\Facades\AdminMenu;
use Igniter\Api\Models\Token;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * API Tokens Admin Controller
 */
class Tokens extends \Admin\Classes\AdminController
{
    public $implement = [
        \Admin\Actions\ListController::class,
    ];

    public $listConfig = [
        'list' => [
            'model' => \Igniter\Api\Models\Token::class,
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

    public function create(Request $request)
    {
        $request->validate([
            'username' => 'required_without:email|alpha_dash',
            'email' => 'required_without:username|email:filter',
            'password' => 'required',
            'device_name' => 'required|alpha_dash',
            'abilities.*' => 'regex:/^[a-zA-Z-_\*]+$/',
        ]);

        $forAdmin = $request->has('username') && !$request->has('email');
        $loginFieldName = $forAdmin ? 'username' : 'email';

        $credentials = [
            $loginFieldName => $request->$loginFieldName,
            'password' => $request->password,
        ];

        $auth = app($forAdmin ? 'admin.auth' : 'auth');
        $user = $auth->getByCredentials($credentials);

        if (!$user || !$auth->validateCredentials($user, $credentials))
            throw ValidationException::withMessages([
                $loginFieldName => ['The provided credentials are incorrect.'],
            ]);

        if (!$user->is_activated)
            throw ValidationException::withMessages([
                $loginFieldName => ['Inactive user account'],
            ]);

        $token = Token::createToken($user, $request->device_name, $request->abilities ?? ['*']);

        return response()->json([
            'status_code' => 201,
            'token' => $token->plainTextToken,
        ])->setStatusCode(201);
    }
}
