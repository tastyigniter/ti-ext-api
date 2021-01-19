<?php

namespace Igniter\Api\ApiResources\Requests;

use Igniter\Api\Classes\ApiRequest;
use Request;

class OrderRequest extends ApiRequest
{
    public function rules()
    {
        $namedRules = [
            ['first_name', 'lang:igniter.cart::default.checkout.label_first_name', 'required|between:1,48'],
            ['last_name', 'lang:igniter.cart::default.checkout.label_last_name', 'required|between:1,48'],
            ['email', 'lang:igniter.cart::default.checkout.label_email', 'sometimes|required|email:filter|max:96|unique:customers'],
            ['telephone', 'lang:igniter.cart::default.checkout.label_telephone', ''],
            ['comment', 'lang:igniter.cart::default.checkout.label_comment', 'max:500'],
            ['payment', 'lang:igniter.cart::default.checkout.label_payment_method', 'sometimes|required|alpha_dash'],
            ['order_type', 'lang:igniter.cart::default.checkout.label_order_type', 'required'],
        ];

        if (Request::input('order_type', 'collection') == 'delivery') {
            $namedRules[] = ['address_id', 'lang:igniter.cart::default.checkout.label_address', 'required|integer'];
            $namedRules[] = ['address.address_1', 'lang:igniter.cart::default.checkout.label_address_1', 'required|min:3|max:128'];
            $namedRules[] = ['address.address_2', 'lang:igniter.cart::default.checkout.label_address_2', 'sometimes|min:3|max:128'];
            $namedRules[] = ['address.city', 'lang:igniter.cart::default.checkout.label_city', 'sometimes|min:2|max:128'];
            $namedRules[] = ['address.state', 'lang:igniter.cart::default.checkout.label_state', 'sometimes|max:128'];
            $namedRules[] = ['address.postcode', 'lang:igniter.cart::default.checkout.label_postcode', 'string'];
            $namedRules[] = ['address.country_id', 'lang:igniter.cart::default.checkout.label_country', 'sometimes|required|integer'];
        }

        return $namedRules;
    }
}
