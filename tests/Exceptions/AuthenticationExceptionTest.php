<?php

namespace Igniter\Api\Tests\Exceptions;

use Igniter\Api\Exceptions\AuthenticationException;

it('renders json response with message and 401 status code', function() {
    $response = (new AuthenticationException('Unauthorized'))->render(null);

    expect($response->getStatusCode())->toBe(401)
        ->and($response->getData(true))->toBe(['message' => 'Unauthorized']);
});
