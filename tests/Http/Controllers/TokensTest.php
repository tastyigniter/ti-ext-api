<?php

namespace Igniter\Api\Tests\Http\Controllers;

it('loads tokens page', function() {
    actingAsSuperUser()
        ->get(route('igniter.api.tokens'))
        ->assertOk();
});

it('deletes token on tokens page', function() {
    $token = \Igniter\Api\Models\Token::factory()->create();

    actingAsSuperUser()
        ->post(route('igniter.api.tokens'), [
            'checked' => [$token->getKey()],
        ], [
            'X-Requested-With' => 'XMLHttpRequest',
            'X-IGNITER-REQUEST-HANDLER' => 'onDelete',
        ])
        ->assertOk();
});
