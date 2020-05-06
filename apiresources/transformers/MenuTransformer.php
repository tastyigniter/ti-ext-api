<?php namespace Igniter\Api\ApiResources\Transformers;

class MenuTransformer extends \Illuminate\Http\Resources\Json\Resource
{
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}