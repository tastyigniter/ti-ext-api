<?php

namespace Igniter\Api\ApiResources\Transformers;

use System\Models\Currencies_model;
use League\Fractal\TransformerAbstract;

class CurrencyTransformer extends TransformerAbstract
{
    public function transform(Currencies_model $currency)
    {
        return $currency->toArray();
    }
}
