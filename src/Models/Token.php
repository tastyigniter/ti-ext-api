<?php

declare(strict_types=1);

namespace Igniter\Api\Models;

use Igniter\Flame\Database\Factories\HasFactory;
use Igniter\User\Models\Customer;
use Igniter\User\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
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
 * @property Carbon|null $last_used_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Model|\Igniter\Flame\Database\Model $tokenable
 * @method static Builder<static>|Token query()
 * @mixin \Igniter\Flame\Database\Model
 */
class Token extends PersonalAccessToken
{
    use HasFactory;

    protected const array DEFAULT_CUSTOMER_ABILITIES = [
        'addresses:*',
        'customers:*',
        'orders:*',
        'reservations:*',
        'reviews:*',
    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'igniter_api_access_tokens';

    public static function customerAbilities(): array
    {
        $abilities = static::DEFAULT_CUSTOMER_ABILITIES;

        $results = Event::dispatch('api.token.extendCustomerAbilities', [&$abilities]);
        if (is_array($results)) {
            foreach ($results as $result) {
                if (is_array($result)) {
                    $abilities = array_merge($abilities, array_values(array_filter($result, fn($item): bool => is_string($item) && $item !== '' && $item !== '*')));
                }
            }
        }

        return array_unique(array_values($abilities));
    }

    public static function isCustomerAbility(string $ability): bool
    {
        if ($ability === '*') {
            return false;
        }

        $prefix = strtolower(Str::before($ability, ':'));
        foreach (static::customerAbilities() as $allowed) {
            if ($ability === $allowed || $prefix === strtolower(Str::before($allowed, ':'))) {
                return true;
            }
        }

        return false;
    }

    public static function sanitizeAbilities($tokenable, array $abilities): array
    {
        if (!$tokenable instanceof Customer) {
            return $abilities === [] ? ['*'] : array_values($abilities);
        }

        if ($abilities === [] || in_array('*', $abilities, true)) {
            return static::customerAbilities();
        }

        return array_values(array_filter($abilities, static::isCustomerAbility(...)));
    }

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
            'abilities' => static::sanitizeAbilities($tokenable, $abilities),
        ]);

        return new NewAccessToken($token, $token->id.'|'.$plainTextToken);
    }

    /**
     * Determine if the token belongs to a admin
     */
    public function isForAdmin(): bool
    {
        return $this->tokenable_type == (new User)->getMorphClass();
    }

    /**
     * Determine if the token belongs to a customer
     */
    public function isForCustomer(): bool
    {
        return $this->tokenable_type == (new Customer)->getMorphClass();
    }
}
