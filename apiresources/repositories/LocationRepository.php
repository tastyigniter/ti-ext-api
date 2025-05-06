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
        if (!is_array($saveData) || !$model) {
            return;
        }

        if (array_key_exists("options",$saveData)) { 
            foreach ($saveData['options'] as $key => $value) {
                $model->setOption($key, $value);
            }
        }

        $this->modelsToSave[] = $model;

        $singularTypes = ['belongsTo', 'hasOne', 'morphOne'];
        foreach ($saveData as $attribute => $value) {
            if ($model->isGuarded($attribute))
                continue;

            $isNested = ($attribute == 'pivot' || (
                    $model->hasRelation($attribute) &&
                    in_array($model->getRelationType($attribute), $singularTypes)
                ));

            if ($isNested && is_array($value) && $model->{$attribute}) {
                $this->setModelAttributes($model->{$attribute}, $value);
            }
            elseif (!starts_with($attribute, '_')) {
                $model->{$attribute} = $value;
            }
        }
    }
}
