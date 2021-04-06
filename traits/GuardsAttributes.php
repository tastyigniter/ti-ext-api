<?php

namespace Igniter\Api\Traits;

use Illuminate\Support\Str;

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

    public function setModelAttribute($key, $value)
    {
        $mutatorMethod = 'set'.Str::studly($key).'Attribute';

        if (method_exists($this, $mutatorMethod))
            return $this->{$mutatorMethod}($value);

        return $this;
    }

    public function getModelAttribute($key, $value)
    {
        if ($this->hasGetMutator($key))
            return $this->mutateAttribute($key, $value);
    }

    /**
     * Determine if a get mutator exists for an attribute.
     *
     * @param string $key
     * @return bool
     */
    public function hasGetMutator($key)
    {
        return method_exists($this, 'get'.Str::studly($key).'Attribute');
    }

    /**
     * Get the value of an attribute using its mutator.
     *
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    protected function mutateAttribute($key, $value)
    {
        return $this->{'get'.Str::studly($key).'Attribute'}($value);
    }
}
