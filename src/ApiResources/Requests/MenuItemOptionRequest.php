<?php

namespace Igniter\Api\ApiResources\Requests;

use Igniter\System\Classes\FormRequest;

class MenuItemOptionRequest extends FormRequest
{
    public function attributes()
    {
        return [
            'menu_id', lang('igniter.cart::default.menus.label_option'),
            'option_id', lang('igniter.cart::default.menus.label_option_id'),
            'priority', lang('igniter.cart::default.menus.label_option'),
            'required', lang('igniter.cart::default.menus.label_option_required'),
            'min_selected', lang('igniter.cart::default.menus.label_min_selected'),
            'max_selected', lang('igniter.cart::default.menus.label_max_selected'),
            'menu_option_values.*', lang('admin::lang.label_option_value_id'),
        ];
    }

    public function rules()
    {
        return [
            'menu_id' => ['nullable', 'integer'],
            'option_id' => ['required', 'integer'],
            'priority' => ['integer'],
            'required' => ['boolean'],
            'min_selected' => ['integer', 'lte:max_selected'],
            'max_selected' => ['integer', 'gte:min_selected'],
            'menu_option_values.*' => ['array'],
        ];
    }
}
