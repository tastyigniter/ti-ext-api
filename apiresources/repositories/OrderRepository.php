<?php

namespace Igniter\Api\ApiResources\Repositories;

use Admin\Models\Orders_model;
use Igniter\Api\Classes\AbstractRepository;

class OrderRepository extends AbstractRepository
{
    protected $modelClass = Orders_model::class;

    protected static $locationAwareConfig = [];

    protected static $customerAwareConfig = [];

    public function beforeSave($model)
    {
        foreach (['order_date', 'order_time', 'location_id', 'processed', 'order_total'] as $field) {
            if ($fieldValue = request()->input($field, false)) {
                $model->$field = $fieldValue;
            }
        }
    }
}
