<?php

declare(strict_types=1);

namespace Igniter\Api\ApiResources\Requests;

use Closure;
use Igniter\Cart\Models\Menu;
use Igniter\Cart\Models\MenuItemOptionValue;
use Igniter\Cart\Models\Stock;
use Igniter\System\Classes\FormRequest;
use Illuminate\Validation\Rule;
use Override;

class StockRequest extends FormRequest
{
    #[Override]
    public function attributes(): array
    {
        return [
            'location_id' => lang('igniter.local::default.label_location_id'),
            'is_tracked' => lang('igniter.cart::default.stocks.label_is_tracked'),
            'low_stock_alert' => lang('igniter.cart::default.stocks.label_low_stock_alert'),
            'low_stock_threshold' => lang('igniter.cart::default.stocks.label_low_stock_threshold'),
            'quantity' => lang('igniter.cart::default.stocks.label_quantity'),
            'stock_action.state' => lang('igniter.cart::default.stocks.label_stock_action'),
            'stock_action.quantity' => lang('igniter.cart::default.stocks.label_stock_quantity'),
        ];
    }

    public function rules(): array
    {
        $rules = [
            'is_tracked' => ['sometimes', 'boolean'],
            'low_stock_alert' => ['sometimes', 'boolean'],
            'low_stock_threshold' => ['sometimes', 'integer', 'min:0'],
        ];

        if (strtolower($this->method()) === 'post') {
            $rules['location_id'] = ['required', 'integer', 'exists:locations,location_id'];
            $rules['stockable_type'] = ['required', 'in:menus,menu_option_values'];
            $rules['stockable_id'] = [
                'required',
                'integer',
                function(string $attribute, mixed $value, Closure $fail): void {
                    $type = $this->input('stockable_type');
                    $exists = match ($type) {
                        'menus' => Menu::query()->whereKey($value)->exists(),
                        'menu_option_values' => MenuItemOptionValue::query()->whereKey($value)->exists(),
                        default => false,
                    };

                    if (!$exists) {
                        $fail('The selected stockable is invalid.');
                    }
                },
                Rule::unique('stocks', 'stockable_id')->where(
                    fn($query) => $query
                        ->where('location_id', $this->input('location_id'))
                        ->where('stockable_type', $this->input('stockable_type')),
                ),
            ];
            $rules['quantity'] = ['sometimes', 'integer', 'min:0'];

            return $rules;
        }

        $rules['stock_action'] = ['sometimes', 'array'];
        $rules['stock_action.state'] = [
            'required_with:stock_action',
            'in:'.implode(',', [
                Stock::STATE_NONE,
                Stock::STATE_IN_STOCK,
                Stock::STATE_RESTOCK,
                Stock::STATE_RECOUNT,
                Stock::STATE_RETURNED,
                Stock::STATE_WASTE,
            ]),
        ];
        $rules['stock_action.quantity'] = [
            'exclude_if:stock_action.state,'.Stock::STATE_NONE,
            'required_with:stock_action',
            'integer',
            'min:0',
        ];
        $rules['out_of_stock_type'] = ['sometimes', 'nullable', 'in:'.Stock::OOS_INDEFINITELY.','.Stock::OOS_CUSTOM];
        $rules['out_of_stock_until'] = ['required_if:out_of_stock_type,'.Stock::OOS_CUSTOM, 'nullable', 'date'];

        return $rules;
    }

    #[Override]
    public function all($keys = null)
    {
        $except = ['quantity', 'stock_action', 'out_of_stock_type', 'out_of_stock_until'];

        if (strtolower($this->method()) !== 'post') {
            $except = array_merge($except, ['location_id', 'stockable_id', 'stockable_type']);
        }

        return array_except(parent::all($keys), $except);
    }
}
