<?php

declare(strict_types=1);

namespace Igniter\Api\ApiResources\Repositories;

use Igniter\Api\Classes\AbstractRepository;
use Igniter\Local\Models\LocationSettings;

class LocationSettingsRepository extends AbstractRepository
{
    protected ?string $modelClass = LocationSettings::class;

    public function update($id, array $attributes = [])
    {
        $model = is_numeric($id) ? $this->find($id) : $id;

        if (!$model) {
            return $model;
        }

        if (array_key_exists('data', $attributes) && is_array($attributes['data'])) {
            $existing = $this->existingSettingsData($model);
            $merged = array_merge($existing, $attributes['data']);
            $attributes['data'] = $merged;

            // LocationSettings::beforeSave writes settingsValues back into data.
            // Keep that bag in sync so a partial `data` payload does not wipe other keys.
            foreach ($merged as $key => $value) {
                $model->setSettingsValue((string)$key, $value);
            }
        }

        return parent::update($model, $attributes);
    }

    /**
     * @return array<string, mixed>
     */
    protected function existingSettingsData(LocationSettings $model): array
    {
        $fromSettings = $model->getSettingsValue();
        if ($fromSettings !== []) {
            return $fromSettings;
        }

        $data = $model->data;

        return is_array($data) ? $data : [];
    }
}
