<?php

namespace Igniter\Api\Models;

use Laravel\Sanctum\HasApiTokens;

/**
 * Users Model
 */
class ApiCustomers extends \Admin\Models\Customers_model
{
    use HasApiTokens;
}