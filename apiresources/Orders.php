<?php

namespace Igniter\Api\ApiResources;

use Igniter\Api\Classes\ApiController;
use Illuminate\Support\Facades\Request;

/**
 * Orders API Controller
 */
class Orders extends ApiController
{
    public $implement = ['Igniter.Api.Actions.RestController'];

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

    public function store()
    {
        if (($token = $this->getToken()) && $token->isForCustomer())
            Request::merge(['customer_id' => $token->tokenable_id]);

        return $this->asExtension('RestController')->store();
    }

    public function update($recordId)
    {
        if (($token = $this->getToken()) && $token->isForCustomer())
            Request::merge(['customer_id' => $token->tokenable_id]);

        return $this->asExtension('RestController')->update($recordId);
    }

    public function restAfterSave($model)
    {
        $requireSave = false;
        foreach (['order_date', 'order_time', 'location_id', 'processed', 'order_total'] as $field) {
            if ($fieldValue = Request::get($field, false)) {
                $model->$field = $fieldValue;
                $requireSave = true;
            }
        }

        if ($orderMenus = (array)Request::get('order_menus', [])) {
            $model->addOrderMenus(json_decode(json_encode($orderMenus)));

            $total_items = 0;
            foreach ($orderMenus as $menuItem) {
                $total_items += $menuItem['qty'];
            }

            $model->total_items = $total_items;
        }

        if ($orderTotals = (array)Request::get('order_totals', []))
            $model->addOrderTotals(json_decode(json_encode($orderTotals), true));

        if ($orderStatus = Request::get('status_id', false))
            $model->updateOrderStatus($orderStatus, ['comment' => Request::get('status_comment', null)]);
    }
}
