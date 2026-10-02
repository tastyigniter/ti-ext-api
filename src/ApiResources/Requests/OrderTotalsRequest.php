<?php

declare(strict_types=1);

namespace Igniter\Api\ApiResources\Requests;

use Igniter\System\Classes\FormRequest;
use Override;

class OrderTotalsRequest extends FormRequest
{
    #[Override]
    public function attributes(): array
    {
        return [
            'location_id' => lang('igniter.local::default.label_location_id'),
            'order_type' => lang('igniter.cart::default.checkout.label_order_type'),
        ];
    }

    public function rules(): array
    {
        return [
            'location_id' => ['required', 'integer'],
            'order_type' => ['required', 'string'],
            'order_menus' => ['required', 'array', 'min:1'],
            'order_menus.*.id' => ['required', 'integer'],
            'order_menus.*.qty' => ['required', 'integer', 'min:1'],
            'order_menus.*.line_id' => ['sometimes', 'nullable', 'string'],
            'order_menus.*.comment' => ['sometimes', 'nullable', 'string'],
            'order_menus.*.options' => ['sometimes', 'array'],
            'order_menus.*.options.*.id' => ['required', 'integer'],
            'order_menus.*.options.*.values' => ['required', 'array'],
            'order_menus.*.options.*.values.*.id' => ['required', 'integer'],
            'order_menus.*.options.*.values.*.qty' => ['sometimes', 'integer', 'min:1'],
        ];
    }
}
