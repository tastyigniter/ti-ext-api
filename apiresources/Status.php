<?php 

namespace Igniter\Api\ApiResources;

use Igniter\Api\Classes\ApiController;

class Status extends ApiController {
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
        'request' => \Admin\Requests\Status::class,
        'repository' => Repositories\StatusRepository::class,
        'transformer' => Transformers\StatusTransformer::class,
    ];

    protected $requiredAbilities = ['status:*'];
}