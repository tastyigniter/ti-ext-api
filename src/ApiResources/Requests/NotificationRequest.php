<?php

declare(strict_types=1);

namespace Igniter\Api\ApiResources\Requests;

use Igniter\System\Classes\FormRequest;

class NotificationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'read' => ['required', 'boolean'],
        ];
    }
}
