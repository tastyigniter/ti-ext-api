<?php

namespace Igniter\Api\ApiResources\Transformers;

use Igniter\Local\Models\Reviews_model;
use League\Fractal\TransformerAbstract;

class ReviewTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'location',
        'customer',
    ];

    public function transform(Reviews_model $review)
    {
        return $review->toArray();
    }

    public function includeCustomer(Reviews_model $review)
    {
        return $this->item($review->customer, new CustomerTransformer, 'customers');
    }

    public function includeLocation(Reviews_model $review)
    {
        return $this->item($review->location, new LocationTransformer, 'locations');
    }
}
