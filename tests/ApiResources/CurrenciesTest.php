<?php

namespace Igniter\Api\Tests\ApiResources;

use Igniter\Admin\Models\User;
use Igniter\System\Models\Currency;
use Laravel\Sanctum\Sanctum;

it('returns all currencies', function () {
    Sanctum::actingAs(User::factory()->create(), ['currencies:*']);

    $this->get(route('igniter.api.currencies.index'))
        ->assertOk()
        ->assertJsonPath('data.0.attributes.currency_name', Currency::listFrontEnd()->first()->currency_name);
});
