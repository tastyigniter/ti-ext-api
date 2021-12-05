<?php

namespace Igniter\Api\ApiResources\Requests;

use System\Classes\FormRequest;

/**
 * @deprecated remove before v4. Added for backward compatibility
 */
class MenuItemOptionRequest extends FormRequest
{
    public function rules()
    {
        return [
            ['menu_id', 'admin::lang.menus.label_option', 'nullable|integer'],
            ['option_id', 'admin::lang.menus.label_option_id', 'required|integer'],
            ['priority', 'admin::lang.menus.label_option', 'integer'],
            ['required', 'admin::lang.menus.label_option_required', 'boolean'],
            ['min_selected', 'admin::lang.menus.label_min_selected', 'integer|lte:max_selected'],
            ['max_selected', 'admin::lang.menus.label_max_selected', 'integer|gte:min_selected'],
            ['menu_option_values.*', 'lang:admin::lang.label_option_value_id', 'array'],
        ];
    }
}
