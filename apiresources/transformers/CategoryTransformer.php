<?php

namespace Igniter\Api\ApiResources\Transformers;

use Igniter\Api\Classes\TransformerAbstract;

class CategoryTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'menus',
        'locations',
    ];

    public function toArray($request)
    {
        return array_merge(parent::toArray($request), [
            'menus' => $this->whenLoaded('menus'),
            'locations' => $this->whenLoaded('locations'),
        ]);
    }
}
