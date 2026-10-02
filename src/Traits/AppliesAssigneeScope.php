<?php

declare(strict_types=1);

namespace Igniter\Api\Traits;

use Igniter\User\Models\Concerns\Assignable;
use Igniter\User\Models\User;
use Illuminate\Database\Eloquent\Builder;

trait AppliesAssigneeScope
{
    protected function extendQuery(Builder $query): void
    {
        $this->applyAssigneeScope($query);
    }

    protected function applyAssigneeScope(Builder $query): void
    {
        if (!in_array(Assignable::class, class_uses_recursive($query->getModel()))) {
            return;
        }

        $user = request()->user();
        if (!$user instanceof User) {
            return;
        }

        if ($user->hasGlobalAssignableScope()) {
            return;
        }

        $query->whereInAssignToGroup($user->groups->pluck('user_group_id')->all());

        if ($user->hasRestrictedAssignableScope()) {
            $query->whereAssignTo($user->getKey());
        }
    }
}
