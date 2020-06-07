<?php namespace Igniter\Api\ApiResources\Transformers;

use Igniter\Api\Classes\TransformerAbstract;

class ReviewTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'location',
        'customer',
    ];

    public function toArray($request)
    {
        return array_merge(parent::toArray($request), [
            'location' => $this->whenLoaded('location'),
            'customer' => $this->whenLoaded('customer'),
        ]);
    }
}