<?php

namespace Igniter\Api\Serializer;

use Igniter\Flame\Support\Str;
use League\Fractal\Serializer\JsonApiSerializer as FractalJsonApiSerializer;

class JsonApiSerializer extends FractalJsonApiSerializer
{
    public function item($resourceKey, array $data)
    {
        // Hacky way of ensuring the id key is always present
        // in the data array, should probably change later
        if (!array_key_exists('id', $data) && $resourceKey) {
            $keyName = Str::singular($resourceKey).'_id';
            $data = array_merge($data, [
                'id' => array_get($data, $keyName),
            ]);

            unset($data[$keyName]);
        }

        return parent::item($resourceKey, $data);
    }
}
