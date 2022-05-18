<?php

namespace Igniter\Api\Classes;

use Igniter\Flame\Database\Model;
use League\Fractal\Scope as FractalScope;

/**
 * Scope
 *
 * The scope class acts as a tracker, relating a specific resource in a specific
 * context. For example, the same resource could be attached to multiple scopes.
 * There are root scopes, parent scopes and child scopes.
 */
class Scope extends FractalScope
{
    protected function fireTransformer($transformer, $data)
    {
        [$transformedData, $includedData] = parent::fireTransformer($transformer, $data);

        if (!array_key_exists('id', $transformedData) && $data instanceof Model)
            $transformedData['id'] = $data->getKey();

        return [$transformedData, $includedData];
    }
}
