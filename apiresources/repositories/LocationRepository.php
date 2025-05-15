<?php

namespace Igniter\Api\ApiResources\Repositories;

use Admin\Models\Locations_model;
use Igniter\Api\Classes\AbstractRepository;

class LocationRepository extends AbstractRepository
{
    protected $modelClass = Locations_model::class;

    protected $hidden = ['location_thumb'];

    protected $guarded = [];

    public function getOptionsAttribute($value)
    {
        return array_except($value, ['hours']);
    }

    protected function extendQuery($query)
    {
        $query->select('*');
    }

    protected function setModelAttributes($model, $saveData)
    {
        parent::setModelAttributes($model, $saveData);
        if (array_key_exists("options",$saveData)) { 
            foreach ($saveData['options'] as $key => $value) {
                $model->setOption($key, $value);
            }
        }
    }
}
