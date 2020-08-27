<?php

namespace Igniter\Api\ApiResources\Transformers;

use Igniter\Api\Classes\TransformerAbstract;

class MenuTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'categories',
        'menu_options',
        'menu_options.menu_option_values',
    ];

    public function toArray($request)
    {
        return array_merge(parent::toArray($request), [
            'categories' => $this->whenLoaded('categories'),
            'menu_options' => $this->whenLoaded('menu_options'),
            'menu_options.menu_option_values' => $this->whenLoaded('menu_options.menu_option_values'),
        ]);
    }
}
