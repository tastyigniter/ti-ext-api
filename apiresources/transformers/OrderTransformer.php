<?php namespace Igniter\Api\ApiResources\Transformers;

class OrderTransformer extends \Illuminate\Http\Resources\Json\Resource
{
    public function toArray($request)
    {
        return array_merge(parent::toArray($request), [
            'order_totals' => $this->resource->getOrderTotals(),
        ]);
    }
}