<?php

namespace Igniter\Api\ApiResources\Transformers;

use Admin\Models\Tables_model;
use League\Fractal\TransformerAbstract;

class TableTransformer extends TransformerAbstract
{
    public function transform(Tables_model $table)
    {
        return $table->toArray();
    }
}
