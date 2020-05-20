<?php

namespace Igniter\Api\Models;

use Illuminate\Support\Str;
use Laravel\Sanctum\NewAccessToken;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * Token Model
 */
class Token extends PersonalAccessToken
{
    /**
     * @var string The database table used by the model.
     */
    public $table = 'igniter_api_access_tokens';

    /**
     * Create a new personal access token for the user.
     *
     * @param \Igniter\Flame\Database\Model $tokenable
     * @param string $name
     * @param array $abilities
     * @return \Laravel\Sanctum\NewAccessToken
     */
    public static function createToken($tokenable, string $name, array $abilities = ['*'])
    {
        $token = $tokenable->tokens()->create([
            'name' => $name,
            'token' => hash('sha256', $plainTextToken = Str::random(80)),
            'abilities' => $abilities,
        ]);

        return new NewAccessToken($token, $token->id.'|'.$plainTextToken);
    }
}