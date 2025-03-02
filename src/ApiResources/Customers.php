<?php

namespace Igniter\Api\ApiResources;

use Igniter\Api\Classes\ApiController;
use Illuminate\Support\Facades\Request;

/**
 * Customers API Controller
 */
class Customers extends ApiController
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
        'request' => \Igniter\User\Http\Requests\CustomerRequest::class,
        'repository' => Repositories\CustomerRepository::class,
        'transformer' => Transformers\CustomerTransformer::class,
    ];

    protected string|array $requiredAbilities = ['customers:*'];

    public function store()
    {
        if (($token = $this->getToken()) && $token->isForCustomer()) {
            Request::merge(['customer_id' => $token->tokenable_id]);
        }

        return $this->asExtension('RestController')->store();
    }
}
