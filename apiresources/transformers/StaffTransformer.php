<?php

namespace Igniter\Api\ApiResources\Transformers;

use Admin\Models\Staffs_model;
use Igniter\Api\ApiResources\Transformers\LocationTransformer;
use Igniter\Api\ApiResources\Transformers\StaffGroupTransformer;
use League\Fractal\TransformerAbstract;

class StaffTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'groups',
        'role',
        'locations',
        'user',
    ];

    public function includeGroups(Staffs_model $staff)
    {
        return $this->collection($staff->groups, new StaffGroupTransformer, 'groups');
    }

    public function includeRole(Staffs_model $staff)
    {
        return $this->item($staff->role, new StaffRoleTransformer, 'role');
    }

    public function includeLocations(Staffs_model $staff)
    {
        return $this->collection($staff->locations, new LocationTransformer, 'locations');
    }

    public function includeUser(Staffs_model $staff)
    {
        return $this->item($staff->user, new UserTransformer, 'user');
    }

    public function transform(Staffs_model $staff)
    {
        return $staff->toArray();
    }

}
