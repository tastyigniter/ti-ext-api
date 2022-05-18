<?php

namespace Igniter\Api\ApiResources\Transformers;

use Igniter\System\Models\Currency;
use League\Fractal\TransformerAbstract;

class CurrencyTransformer extends TransformerAbstract
{
    public function transform(Currency $currency)
    {
        return $currency->toArray();
    }
}
