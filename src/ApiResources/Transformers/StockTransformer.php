<?php

namespace Igniter\Api\ApiResources\Transformers;

use Igniter\Admin\Models\Stock;
use League\Fractal\TransformerAbstract;

class StockTransformer extends TransformerAbstract
{
    public function transform(Stock $stock)
    {
        return array_merge($stock->toArray(), []);
    }
}
