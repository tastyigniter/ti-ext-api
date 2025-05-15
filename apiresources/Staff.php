<?php 

namespace Igniter\Api\ApiResources;

use Igniter\Api\Classes\ApiController;

class Staff extends ApiController {
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
        'request' => \Admin\Requests\Staff::class,
        'repository' => Repositories\StaffRepository::class,
        'transformer' => Transformers\StaffTransformer::class,
    ];

    protected $requiredAbilities = ['staff:*'];
}