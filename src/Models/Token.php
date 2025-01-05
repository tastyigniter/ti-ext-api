<?php

declare(strict_types=1);

namespace Igniter\Api\Models;

use Igniter\Flame\Database\Factories\HasFactory;
use Igniter\User\Models\Customer;
use Igniter\User\Models\User;
use Illuminate\Support\Str;
use Laravel\Sanctum\NewAccessToken;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * Token Model
 *
 * @property int $id
 * @property string $tokenable_type
 * @property int $tokenable_id
 * @property string $name
 * @property string $token
 * @property array|null $abilities
 * @property \Illuminate\Support\Carbon|null $last_used_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Igniter\Flame\Database\Model $tokenable
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Token query()
 * @mixin \Igniter\Flame\Database\Model
 */
class Token extends PersonalAccessToken
{
    use HasFactory;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'igniter_api_access_tokens';

    /**
     * Create a new personal access token for the user.
     *
     * @param \Igniter\Flame\Database\Model $tokenable
     */
    public static function createToken($tokenable, string $name, array $abilities = ['*']): NewAccessToken
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
     */
    public function isForAdmin(): bool
    {
        return $this->tokenable_type == User::make()->getMorphClass();
    }

    /**
     * Determine if the token belongs to a customer
     */
    public function isForCustomer(): bool
    {
        return $this->tokenable_type == Customer::make()->getMorphClass();
    }
}
