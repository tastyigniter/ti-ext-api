<?php

declare(strict_types=1);

namespace Igniter\Api\Http\Controllers;

use Igniter\User\Auth\Models\User as AuthUser;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RevokeToken
{
    public function __invoke(Request $request): Response
    {
        /** @var AuthUser|null $user */
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'status_code' => 401,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $token = $user->currentAccessToken();
        if ($token) {
            $token->delete();
        }

        return response()->noContent();
    }
}
