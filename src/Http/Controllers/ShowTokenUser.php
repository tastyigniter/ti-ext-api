<?php

declare(strict_types=1);

namespace Igniter\Api\Http\Controllers;

use Igniter\User\Models\Customer;
use Igniter\User\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ShowTokenUser
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'status_code' => 401,
                'user' => null,
            ], 401);
        }

        return response()->json([
            'status_code' => 200,
            'user' => $this->serializeUser($user),
        ]);
    }

    protected function serializeUser(User|Customer $user): array
    {
        if ($user instanceof User) {
            $user->loadMissing(['role', 'groups', 'locations']);

            return [
                'id' => $user->getKey(),
                'user_id' => $user->getKey(),
                'name' => $user->name,
                'email' => $user->email,
                'username' => $user->username,
                'super_user' => (bool)$user->super_user,
                'sale_permission' => (int)$user->sale_permission,
                'role' => $user->role ? [
                    'id' => $user->role->getKey(),
                    'name' => $user->role->name,
                ] : null,
                'groups' => $user->groups
                    ->map(fn($group): array => [
                        'id' => $group->getKey(),
                        'name' => $group->user_group_name,
                    ])
                    ->values()
                    ->all(),
                'assigned_locations' => $user->isSuperUser()
                    ? []
                    : $user->locations
                        ->map(fn($location): array => [
                            'id' => $location->getKey(),
                            'name' => $location->location_name,
                        ])
                        ->values()
                        ->all(),
            ];
        }

        return $user->toArray();
    }
}
