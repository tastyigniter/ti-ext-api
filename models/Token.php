<?php

namespace Igniter\Api\Models;

use Admin\Models\Customers_model;
use Admin\Models\Users_model;
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

    /**
     * Determine if the token belongs to a admin
     *
     * @return bool
     */
    public function isForAdmin()
    {
        return $this->tokenable_type == Users_model::make()->getMorphClass();
    }

    /**
     * Determine if the token belongs to a customer
     *
     * @return bool
     */
    public function isForCustomer()
    {
        return $this->tokenable_type == Customers_model::make()->getMorphClass();
    }

    /**
     * Determine if the token has a given ability.
     *
     * @param mixed $ability
     * @return bool
     */
    public function can($ability)
    {
        if (!is_array($ability))
            $ability = [$ability];

        if (in_array('*', $this->abilities))
            return true;

        $diff = array_diff_key(array_flip($ability), array_flip($this->abilities));

        return count($diff) != count($ability);
    }
}
