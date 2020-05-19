<?php

namespace Igniter\Api\Models;

use Laravel\Sanctum\PersonalAccessToken;

/**
 * Resource Model
 */
class Token extends PersonalAccessToken
{
    /**
     * @var string The database table used by the model.
     */
    public $table = 'igniter_api_access_tokens';
}