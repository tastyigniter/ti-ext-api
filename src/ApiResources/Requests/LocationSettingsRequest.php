<?php

declare(strict_types=1);

namespace Igniter\Api\ApiResources\Requests;

use Igniter\System\Classes\FormRequest;
use Override;

class LocationSettingsRequest extends FormRequest
{
    #[Override]
    public function attributes(): array
    {
        return [
            'location_id' => lang('igniter.local::default.label_location_id'),
        ];
    }

    public function rules(): array
    {
        $updating = $this->isMethod('PUT') || $this->isMethod('PATCH');

        return [
            'location_id' => [$updating ? 'sometimes' : 'required', 'integer'],
            'item' => [$updating ? 'sometimes' : 'required', 'string'],
            'data' => ['required', 'array'],
        ];
    }
}
