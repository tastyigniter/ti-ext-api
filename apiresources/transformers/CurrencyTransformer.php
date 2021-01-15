<?php

namespace Igniter\Api\ApiResources\Transformers;

use League\Fractal\TransformerAbstract;
use System\Models\Currencies_model;

class CurrencyTransformer extends TransformerAbstract
{
    public function transform(Currencies_model $currency)
    {
        return $currency->toArray();
    }
}
