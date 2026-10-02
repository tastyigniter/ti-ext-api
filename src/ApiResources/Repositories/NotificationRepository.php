<?php

declare(strict_types=1);

namespace Igniter\Api\ApiResources\Repositories;

use Igniter\Api\Classes\AbstractRepository;
use Igniter\User\Models\Notification;

class NotificationRepository extends AbstractRepository
{
    protected ?string $modelClass = Notification::class;

    protected static $customerAwareConfig = [];
}
