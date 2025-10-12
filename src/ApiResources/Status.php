<?php 

namespace Igniter\Api\ApiResources;

use Igniter\Api\Classes\ApiController;
use Igniter\Api\Http\Actions\RestController;

class Status extends ApiController {
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
        'request' => \Admin\Requests\Status::class,
        'repository' => Repositories\StatusRepository::class,
        'transformer' => Transformers\StatusTransformer::class,
    ];

    protected string|array $requiredAbilities = ['status:*'];
}