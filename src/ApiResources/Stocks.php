<?php

declare(strict_types=1);

namespace Igniter\Api\ApiResources;

use Igniter\Api\ApiResources\Repositories\StockRepository;
use Igniter\Api\ApiResources\Requests\StockRequest;
use Igniter\Api\ApiResources\Transformers\StockTransformer;
use Igniter\Api\Classes\ApiController;
use Igniter\Api\Http\Actions\RestController;
use Igniter\Cart\Models\Stock;
use Illuminate\Support\Carbon;

/**
 * Stocks API Controller
 */
class Stocks extends ApiController
{
    public array $implement = [RestController::class];

    public $restConfig = [
        'actions' => [
            'index' => [
                'pageLimit' => 20,
            ],
            'store' => [],
            'show' => [],
            'update' => [],
        ],
        'request' => StockRequest::class,
        'repository' => StockRepository::class,
        'transformer' => StockTransformer::class,
    ];

    protected string|array $requiredAbilities = ['stocks:*'];

    public function restAfterSave($model): void
    {
        if (!$model instanceof Stock) {
            return;
        }

        $userId = $this->user()?->getKey();
        $creating = request()->isMethod('POST');

        if ($creating && request()->exists('quantity') && $model->is_tracked) {
            $model->updateStock((int)request()->input('quantity'), Stock::STATE_RECOUNT, [
                'user_id' => $userId,
            ]);
        }

        $state = request()->input('stock_action.state');
        if (!$creating && is_string($state) && $state !== '' && $state !== Stock::STATE_NONE) {
            $model->updateStock((int)request()->input('stock_action.quantity', 0), $state, [
                'user_id' => $userId,
            ]);
        }

        if (!$creating && request()->exists('out_of_stock_type')) {
            $type = request()->input('out_of_stock_type');
            if ($type === null || $type === '') {
                $model->clearOutOfStockOverride();
            } elseif ($model->is_tracked) {
                $until = request()->input('out_of_stock_until');
                $model->applyOutOfStockOverride(
                    (string)$type,
                    $until ? Carbon::parse($until) : null,
                );
            }
        }

        $model->unsetRelation('stockable');
        $model->refresh();
        $model->load('stockable');
    }
}
