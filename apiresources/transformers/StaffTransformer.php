<?php

namespace Igniter\Api\ApiResources\Transformers;

use Admin\Models\Staffs_model;
use League\Fractal\TransformerAbstract;

class StaffTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'groups',
        'role'
    ];
    
    public function transform(Staffs_model $staff)
    {
        return $staff->toArray();
    }

    public function includeGroups(Staffs_model $staff)
    {
        return $this->collection($staff->groups, new StaffGroupTransformer, 'groups');
    }

    public function includeRole(Staffs_model $staff)
    {
        return $this->item($staff->role, new StaffRoleTransformer, 'role');
    }
}
