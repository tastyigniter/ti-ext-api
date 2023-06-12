<?php

namespace Igniter\Api\ApiResources\Transformers;

use Igniter\PayRegister\Models\Payment;
use League\Fractal\TransformerAbstract;

class PaymentMethodTransformer extends TransformerAbstract
{
    public function transform(Payment $payment)
    {
        return [
            'payment_id' => $payment->getKey(),
            'code' => $payment->code,
            'name' => $payment->name,
            'description' => $payment->description,
        ];
    }
}
