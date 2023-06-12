<?php

namespace Igniter\Api\Tests\ApiResources;

use Igniter\System\Models\Currency;
use Igniter\User\Models\User;
use Laravel\Sanctum\Sanctum;

it('returns all currencies', function () {
    Sanctum::actingAs(User::factory()->create(), ['currencies:*']);

    $this->get(route('igniter.api.currencies.index'))
        ->assertOk()
        ->assertJsonPath('data.0.attributes.currency_name', Currency::listFrontEnd()->first()->currency_name);
});
