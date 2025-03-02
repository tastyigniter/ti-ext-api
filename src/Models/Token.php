<?php

namespace Igniter\Api\Models;

use Igniter\User\Models\Customer;
use Igniter\User\Models\User;
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

    /**
     * Determine if the token belongs to a admin
     *
     * @return bool
     */
    public function isForAdmin()
    {
        return $this->tokenable_type == User::make()->getMorphClass();
    }

    /**
     * Determine if the token belongs to a customer
     *
     * @return bool
     */
    public function isForCustomer()
    {
        return $this->tokenable_type == Customer::make()->getMorphClass();
    }
}
