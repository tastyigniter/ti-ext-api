<?php

namespace Igniter\Api\ApiResources\Transformers;

use Igniter\Api\Traits\MergesIdAttribute;
use Igniter\System\Models\Currency;
use League\Fractal\TransformerAbstract;

class CurrencyTransformer extends TransformerAbstract
{
    use MergesIdAttribute;

    public function transform(Currency $currency)
    {
        return $this->mergesIdAttribute($currency);
    }
}
