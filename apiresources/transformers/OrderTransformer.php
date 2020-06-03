<?php namespace Igniter\Api\ApiResources\Transformers;

use Igniter\Api\Classes\TransformerAbstract;

class OrderTransformer extends TransformerAbstract
{
    public function toArray($request)
    {
        return array_merge(parent::toArray($request), [
            'order_totals' => $this->resource->getOrderTotals(),
        ]);
    }
}