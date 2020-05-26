<?php namespace Igniter\Api\ApiResources\Transformers;

class OrderTransformer extends \Illuminate\Http\Resources\Json\Resource
{
    public static function collection($results)
    {
	    foreach ($results as $result)
	    {
		    $result->order_totals = $result->getOrderTotals();
	    }
	    
	    return parent::collection($results);
    }
	
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}