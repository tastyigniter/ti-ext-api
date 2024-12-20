<?php

namespace Igniter\Api\ApiResources\Transformers;

use Igniter\Api\Traits\MergesIdAttribute;
use Igniter\Cart\Models\Category;
use League\Fractal\TransformerAbstract;

class CategoryTransformer extends TransformerAbstract
{
    use MergesIdAttribute;

    protected array $availableIncludes = [
        'media',
        'menus',
        'locations',
    ];

    public function transform(Category $category)
    {
        return $this->mergesIdAttribute($category);
    }

    public function includeMedia(Category $category)
    {
        return ($thumb = $category->getFirstMedia()) ? $this->item($thumb, new MediaTransformer, 'media') : null;
    }

    public function includeMenus(Category $category)
    {
        return $this->collection($category->menus, new MenuTransformer, 'menus');
    }

    public function includeLocations(Category $category)
    {
        return $this->collection($category->locations, new LocationTransformer, 'locations');
    }
}
