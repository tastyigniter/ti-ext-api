<?php

namespace Igniter\Api\ApiResources\Requests;

use Illuminate\Support\Facades\Request;
use System\Classes\FormRequest;

class OptionRequest extends FormRequest
{
    public function rules()
    {
        $method = Request::method();
        $namedRules = [
            ['option_name', 'lang:admin::lang.menu_options.label_option_name', 'required|min:2|max:32'],
            ['display_type', 'lang:admin::lang.menu_options.label_display_type', 'required|alpha'],
            ['locations.*', 'lang:admin::lang.label_location', 'integer'],
        ];
        return $namedRules;
    }
}