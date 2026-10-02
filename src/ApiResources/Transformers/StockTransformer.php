<?php

declare(strict_types=1);

namespace Igniter\Api\ApiResources\Transformers;

use Igniter\Api\Traits\MergesIdAttribute;
use Igniter\Cart\Models\Stock;
use League\Fractal\TransformerAbstract;

class StockTransformer extends TransformerAbstract
{
    use MergesIdAttribute;

    public function transform(Stock $stock): array
    {
        return array_merge($this->mergesIdAttribute($stock), [
            'stockable_name' => $stock->stockable_name,
        ]);
    }
}
