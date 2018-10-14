<?php namespace Igniter\Api\Rest\Transformers;

use igniter\api\Models\Resource;

class DummyTransformer extends \League\Fractal\TransformerAbstract
{
    protected $availableIncludes = [
        
    ];

	public function transform(\igniter\api\Models\Resource $model)
	{
	    return $model->toArray();
	}
}