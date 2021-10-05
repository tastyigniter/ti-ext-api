<?php

namespace Igniter\Api\ApiResources\Repositories;

use Admin\Models\Menu_item_options_model;
use Igniter\Api\Classes\AbstractRepository;

class MenuItemOptionRepository extends AbstractRepository
{
    protected $modelClass = Menu_item_options_model::class;

    public function create($model, array $attributes)
    {
        $this->fill($model, $attributes);
        return $this->create($model, $attributes);
    }

    public function update($id, array $attributes = [])
    {
        $model = is_numeric($id) ? $this->find($id) : $id;

        if (!$model) return $model;

        $this->fireSystemEvent('api.repository.beforeUpdate', [$model, $attributes]);

        $this->fill($model, $attributes);

        $updated = $model->save();

        $this->fireSystemEvent('api.repository.afterUpdate', [$model, $updated]);

        return $model;
    }

    private function fill($model, $attributes) {
        $model->fill($attributes);
        if (isset($attributes['menu_option_values']))
            $model->menu_option_values = $attributes['menu_option_values'];
    }
}
