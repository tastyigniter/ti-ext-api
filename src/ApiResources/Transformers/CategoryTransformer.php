<?php

namespace Igniter\Api\ApiResources\Transformers;

use Igniter\Cart\Models\Category;
use League\Fractal\TransformerAbstract;

class CategoryTransformer extends TransformerAbstract
{
    protected array $availableIncludes = [
        'media',
        'menus',
        'locations',
    ];

    public function transform(Category $category)
    {
        return $category->toArray();
    }

    public function includeMedia(Category $category)
    {
        if (!$thumb = $category->getFirstMedia()) {
            return null;
        }

        return $this->item($thumb, new MediaTransformer, 'media');
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
