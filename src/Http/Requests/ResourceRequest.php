<?php

namespace Igniter\Api\Http\Requests;

use Igniter\System\Classes\FormRequest;

class ResourceRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => ['required', 'min:2', 'max:128', 'string'],
            'description' => ['required', 'min:2', 'max:255'],
            'endpoint' => ['max:255', 'regex:/^[a-z0-9\-_\/]+$/i', 'unique:igniter_api_resources,endpoint,'.$this->getRecordId()],
            'meta' => ['array'],
            'meta.actions.*' => ['alpha'],
            'meta.authorization.*' => ['alpha'],
        ];
    }
}
