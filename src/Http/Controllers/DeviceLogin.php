<?php

declare(strict_types=1);

namespace Igniter\Api\Http\Controllers;

use Igniter\Api\Classes\LoginCodeManager;
use Igniter\Flame\Exception\ApplicationException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

class DeviceLogin extends Controller
{
    public function __invoke(Request $request, LoginCodeManager $loginCodes): Response
    {
        $request->validate([
            'code' => ['required', 'string'],
            'device_name' => ['required', 'string', 'max:255'],
            'abilities' => ['required', 'array', 'min:1'],
            'abilities.*' => ['required', 'string', 'regex:/^[a-zA-Z-_\*\.\:]+$/'],
        ]);

        try {
            $result = $loginCodes->redeem(
                (string)$request->input('code'),
                (string)$request->input('device_name'),
                array_values($request->input('abilities', [])),
            );
        } catch (ApplicationException $ex) {
            return response()->json([
                'message' => $ex->getMessage(),
            ], 401);
        }

        return response()->json([
            'status_code' => 201,
            'token' => $result['token'],
            'email' => $result['email'],
        ], 201);
    }
}
