<?php

declare(strict_types=1);

namespace Igniter\Api\ApiResources\Repositories;

use Igniter\Api\Classes\AbstractRepository;
use Igniter\Cart\Models\Stock;
use Illuminate\Database\Eloquent\Builder;

class StockRepository extends AbstractRepository
{
    protected ?string $modelClass = Stock::class;

    protected static $locationAwareConfig = [];

    protected static $customerAwareConfig = [];

    protected function extendQuery(Builder $query): void
    {
        $query->with('stockable');
    }
}
