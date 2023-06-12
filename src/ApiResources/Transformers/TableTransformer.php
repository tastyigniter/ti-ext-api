<?php

namespace Igniter\Api\ApiResources\Transformers;

use Igniter\Reservation\Models\Table;
use League\Fractal\TransformerAbstract;

class TableTransformer extends TransformerAbstract
{
    public function transform(Table $table)
    {
        return $table->toArray();
    }
}
