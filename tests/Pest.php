<?php

use Igniter\User\Models\User;

uses(SamPoyigi\Testbench\TestCase::class)->in(__DIR__);

function actingAsSuperUser()
{
    return test()->actingAs(User::factory()->superUser()->create(), 'igniter-admin');
}
