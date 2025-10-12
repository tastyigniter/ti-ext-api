<?php 

namespace Igniter\Api\ApiResources;

use Igniter\Api\Classes\ApiController;
use Igniter\Api\Http\Actions\RestController;
use Igniter\User\Http\Requests\UserRequest;

class Users extends ApiController {
    public array $implement = [RestController::class];

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
        'request' => UserRequest::class,
        'repository' => Repositories\UserRepository::class,
        'transformer' => Transformers\UserTransformer::class,
    ];

    protected string|array $requiredAbilities = ['staff:*'];
}