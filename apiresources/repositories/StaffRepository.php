<?php

namespace DineAByte\MoreAPI\ApiResources\Repositories;

use Admin\Models\Staffs_model;
use Igniter\Api\Classes\AbstractRepository;

class StaffRepository extends AbstractRepository
{
    protected $modelClass = Staffs_model::class;

    protected static $locationAwareConfig = [];
}
