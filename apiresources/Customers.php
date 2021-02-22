<?php

namespace Igniter\Api\ApiResources;

use Igniter\Api\Classes\ApiController;
use Illuminate\Support\Facades\Request;

/**
 * Customers API Controller
 */
class Customers extends ApiController
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
        'request' => \Admin\Requests\Customer::class,
        'repository' => Repositories\CustomerRepository::class,
        'transformer' => Transformers\CustomerTransformer::class,
    ];

    protected $requiredAbilities = ['customers:*'];

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
}
