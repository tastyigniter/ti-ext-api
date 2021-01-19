<?php

namespace Igniter\Api\ApiResources\Transformers;

use Admin\Models\Payments_model;
use League\Fractal\TransformerAbstract;

class PaymentMethodTransformer extends TransformerAbstract
{
    public function transform(Payments_model $payment)
    {
        return [
            'payment_id' => $payment->getKey(),
            'code' => $payment->code,
            'name' => $payment->name,
            'description' => $payment->description,
        ];
    }
}
