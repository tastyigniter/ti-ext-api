<?php

declare(strict_types=1);

namespace Igniter\Api\ApiResources;

use Igniter\Api\Classes\ApiController;

/**
 * Orders API Controller
 */
class Orders extends ApiController
{
    public array $implement = [\Igniter\Api\Http\Actions\RestController::class];

    public $restConfig = [
        'actions' => [
            'index' => [
                'pageLimit' => 20,
            ],
            'store' => [],
            'show' => [],
            'update' => [],
            'destroy' => [],
        ],
        'request' => Requests\OrderRequest::class,
        'repository' => Repositories\OrderRepository::class,
        'transformer' => Transformers\OrderTransformer::class,
    ];

    protected string|array $requiredAbilities = ['orders:*'];

    public function restAfterSave($model): void
    {
        if ($orderMenus = (array)request()->input('order_menus', [])) {
            $model->addOrderMenus(json_decode(json_encode($orderMenus)));

            $total_items = 0;
            foreach ($orderMenus as $menuItem) {
                $total_items += $menuItem['qty'];
            }

            $model->total_items = $total_items;
        }

        if ($orderTotals = (array)request()->input('order_totals', [])) {
            $model->addOrderTotals(json_decode(json_encode($orderTotals), true));
        }

        if ($orderStatus = request()->input('status_id', false)) {
            $model->updateOrderStatus($orderStatus, ['comment' => request()->input('status_comment')]);
        }

        if (request()->input('processed', false)) {
            $model->markAsPaymentProcessed();
        }
    }
}
