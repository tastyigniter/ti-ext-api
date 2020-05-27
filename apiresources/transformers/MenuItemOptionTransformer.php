<?php namespace Igniter\Api\ApiResources\Transformers;

class MenuItemOptionTransformer extends \Illuminate\Http\Resources\Json\Resource
{
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}