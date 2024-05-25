<?php

namespace Igniter\Api\Http\Controllers;

use Igniter\Api\Models\Token;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;

class CreateToken extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email:filter'],
            'password' => 'required',
            'is_admin' => 'boolean',
            'device_name' => ['required', 'string', 'max:255'],
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
