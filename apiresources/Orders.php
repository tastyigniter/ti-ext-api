<?php

namespace Igniter\Api\ApiResources;

use Igniter\Api\Classes\ApiController;
use Illuminate\Support\Facades\Request;

/**
 * Orders API Controller
 */
class Orders extends ApiController
{
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

        $this->asExtension('RestController')->store();
    }

    public function update($recordId)
    {
        if (($token = $this->getToken()) && $token->isForCustomer())
            Request::merge(['customer_id' => $token->tokenable_id]);

        $this->asExtension('RestController')->update($recordId);
    }

    public function restAfterSave($model)
    {
        if ($menuItems = (array)Request::get('menu_items', []))
            $model->addOrderMenus(json_decode(json_encode($menuItems)));
    }
}
