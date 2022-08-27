<?php

namespace Igniter\Api\ApiResources\Transformers;

use Admin\Models\Stocks_model;
use League\Fractal\TransformerAbstract;

class StockTransformer extends TransformerAbstract
{
    public function transform(Stocks_model $stock)
    {
        return array_merge($stock->toArray(), []);
    }
}
