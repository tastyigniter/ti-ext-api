<?php

namespace Igniter\Api\ApiResources\Transformers;

use Igniter\Local\Models\Review;
use League\Fractal\TransformerAbstract;

class ReviewTransformer extends TransformerAbstract
{
    protected array $availableIncludes = [
        'location',
        'customer',
    ];

    public function transform(Review $review)
    {
        return $review->toArray();
    }

    public function includeCustomer(Review $review)
    {
        return $this->item($review->customer, new CustomerTransformer, 'customers');
    }

    public function includeLocation(Review $review)
    {
        return $this->item($review->location, new LocationTransformer, 'locations');
    }
}
