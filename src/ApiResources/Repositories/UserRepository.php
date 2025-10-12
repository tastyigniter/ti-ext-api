<?php

namespace Igniter\Api\ApiResources\Repositories;

use Igniter\Admin\Models\Staff;
use Igniter\User\Models\User;
use Igniter\Api\Classes\AbstractRepository;

class UserRepository extends AbstractRepository
{
    protected ?string $modelClass = User::class;

    protected static $locationAwareConfig = [];
}