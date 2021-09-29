<?php

namespace Igniter\Api\ApiResources\Requests;

use Illuminate\Support\Facades\Request;
use System\Classes\FormRequest;

class MenuOptionRequest extends FormRequest
{
    public function rules()
    {
        return [
            ['option_name', 'lang:admin::lang.menu_options.label_option_name', 'required|min:2|max:32'],
            ['display_type', 'lang:admin::lang.menu_options.label_display_type', 'required|alpha'],
            ['priority', 'lang:admin::lang.menu_options.label_priority', 'integer'],
            ['locations.*', 'lang:admin::lang.label_location', 'integer'],
            ['option_values.*.option_value_id', 'lang:admin::lang.label_option_value_id', 'integer'],
            ['option_values.*.option_id', 'lang:admin::lang.label_option_id', 'integer'],
            ['option_values.*.value', 'lang:admin::lang.menu_options.label_option_value', 'min:2|max:128'],
            ['option_values.*.price', 'lang:admin::lang.menu_options.label_option_price', 'numeric|min:0'],
            ['option_values.*.priority', 'lang:admin::lang.menu_options.label_option_price', 'integer'],
            ['option_values.*.allergens.*', 'lang:admin::lang.menus.label_allergens', 'integer'],
        ];
    }
}
