<?php

declare(strict_types=1);

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

    public function transform(Category $category): array
    {
        return $this->mergesIdAttribute($category);
    }

    public function includeMedia(Category $category): ?\League\Fractal\Resource\Item
    {
        return ($thumb = $category->getFirstMedia()) ? $this->item($thumb, new MediaTransformer, 'media') : null;
    }

    public function includeMenus(Category $category): ?\League\Fractal\Resource\Collection
    {
        return $this->collection($category->menus, new MenuTransformer, 'menus');
    }

    public function includeLocations(Category $category): ?\League\Fractal\Resource\Collection
    {
        return $this->collection($category->locations, new LocationTransformer, 'locations');
    }
}
