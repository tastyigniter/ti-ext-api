<?php

declare(strict_types=1);

namespace Igniter\Api\Models;

use Igniter\Flame\Database\Model;
use Igniter\User\Models\User;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $code_hash
 * @property Carbon|null $used_at
 * @property User|null $user
 */
class LoginCode extends Model
{
    public $table = 'igniter_api_login_codes';

    protected $guarded = [];

    protected $casts = [
        'user_id' => 'integer',
        'used_at' => 'datetime',
    ];

    public $relation = [
        'belongsTo' => [
            'user' => [User::class, 'foreignKey' => 'user_id', 'otherKey' => 'user_id'],
        ],
    ];

    public $timestamps = true;

    public function isUsed(): bool
    {
        return $this->used_at !== null;
    }
}
