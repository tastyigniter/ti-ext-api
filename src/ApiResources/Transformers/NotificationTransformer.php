<?php

declare(strict_types=1);

namespace Igniter\Api\ApiResources\Transformers;

use Igniter\User\Models\Notification;
use League\Fractal\TransformerAbstract;

class NotificationTransformer extends TransformerAbstract
{
    public function transform(Notification $notification): array
    {
        return [
            'id' => (string)$notification->getKey(),
            'type' => $notification->type,
            'title' => $notification->title,
            'message' => $notification->message,
            'url' => $notification->url,
            'icon' => $notification->icon,
            'read_at' => $notification->read_at?->toJSON(),
            'created_at' => $notification->created_at?->toJSON(),
            'updated_at' => $notification->updated_at?->toJSON(),
        ];
    }
}
