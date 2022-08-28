<?php

namespace Igniter\Api\ApiResources;

use Igniter\Api\Classes\ApiController;

/**
 * Orders API Controller
 */
class Orders extends ApiController
{
    public $implement = [\Igniter\Api\Http\Actions\RestController::class];

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

    protected $requiredAbilities = ['orders:*'];

    public function restExtendQuery($query)
    {
        if (($token = $this->getToken()) && $token->isForCustomer())
            $query->where('customer_id', $token->tokenable_id);

        return $query;
    }

    public function restAfterSave($model)
    {
        if ($orderMenus = (array)request()->input('order_menus', [])) {
            $model->addOrderMenus(json_decode(json_encode($orderMenus)));

            $total_items = 0;
            foreach ($orderMenus as $menuItem) {
                $total_items += $menuItem['qty'];
            }

            $model->total_items = $total_items;
        }

        if ($orderTotals = (array)request()->input('order_totals', []))
            $model->addOrderTotals(json_decode(json_encode($orderTotals), true));

        if ($orderStatus = request()->input('status_id', false))
            $model->updateOrderStatus($orderStatus, ['comment' => request()->input('status_comment', null)]);
    }
}
