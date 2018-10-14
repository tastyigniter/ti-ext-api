<?php namespace Igniter\Api\Resources\Transformers;

class DummyTransformer extends \League\Fractal\TransformerAbstract
{
    protected $availableIncludes = [

    ];

    public function transform(\igniter\api\Models\Resource $model)
    {
        return $model->toArray();
    }
}