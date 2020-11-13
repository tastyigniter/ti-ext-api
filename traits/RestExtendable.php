<?php

namespace Igniter\Api\Traits;

trait RestExtendable
{
    /**
     * Run logic before the store or update resource operation
     * by overriding it in the controller.
     * @param \Model $model
     * @return void
     */
    public function restBeforeSave($model)
    {
    }

    /**
     * Run logic before the store resource operation
     * by overriding it in the controller.
     * @param \Model $model
     * @return void
     */
    public function restBeforeCreate($model)
    {
    }

    /**
     * Run logic before the update resource operation
     * by overriding it in the controller.
     * @param \Model $model
     * @return void
     */
    public function restBeforeUpdate($model)
    {
    }

    /**
     * Run logic after the store or update resource operation
     * by overriding it in the controller.
     * @param \Model $model
     * @return void
     */
    public function restAfterSave($model)
    {
    }

    /**
     * Run logic after the store resource operation
     * by overriding it in the controller.
     * @param \Model $model
     * @return void
     */
    public function restAfterCreate($model)
    {
    }

    /**
     * Run logic after the update resource operation
     * by overriding it in the controller.
     * @param \Model $model
     * @return void
     */
    public function restAfterUpdate($model)
    {
    }

    /**
     * Run logic after the delete resource operation
     * by overriding it in the controller.
     * @param \Model $model
     * @return void
     */
    public function restAfterDelete($model)
    {
    }

    /**
     * Extend supplied model query, the model query can
     * be altered by overriding it in the controller.
     * @param \Igniter\Flame\Database\Builder $query
     * @return void
     */
    public function restExtendQuery($query)
    {
    }
}
