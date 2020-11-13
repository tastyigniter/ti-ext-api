<?php

namespace Igniter\Api\Traits;

trait GuardsAttributes
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that are hidden from response.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that are visible in response.
     *
     * @var array
     */
    protected $visible = [];

    /**
     * Get the fillable attributes for the model.
     *
     * @return array
     */
    public function getFillable()
    {
        return $this->fillable;
    }

    /**
     * Get the guarded attributes for the model.
     *
     * @return array
     */
    public function getGuarded()
    {
        return $this->guarded;
    }

    public function getHidden()
    {
        return $this->hidden;
    }

    public function getVisible()
    {
        return $this->visible;
    }
}
