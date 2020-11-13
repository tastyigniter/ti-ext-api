<?php

namespace Igniter\Api\ApiResources\Transformers;

use Admin\Models\Categories_model;
use League\Fractal\TransformerAbstract;

class CategoryTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'menus',
        'locations',
    ];

    public function transform(Categories_model $category)
    {
        return $category->toArray();
    }

    public function includeMenus(Categories_model $category)
    {
        return $this->collection($category->menus, new MenuTransformer, 'menus');
    }

    public function includeLocations(Categories_model $category)
    {
        return $this->collection($category->locations, new LocationTransformer, 'locations');
    }
}
