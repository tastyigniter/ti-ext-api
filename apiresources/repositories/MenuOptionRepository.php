<?php

namespace Igniter\Api\ApiResources\Repositories;

use Admin\Models\Menu_options_model;
use Igniter\Api\Classes\AbstractRepository;

class MenuOptionRepository extends AbstractRepository
{
    protected $modelClass = Menu_options_model::class;

    protected static $locationAwareConfig = [];
}
