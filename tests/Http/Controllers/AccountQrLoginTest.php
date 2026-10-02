<?php

declare(strict_types=1);

namespace Igniter\Api\Tests\Http\Controllers;

use Igniter\Api\Models\LoginCode;
use Igniter\User\Http\Controllers\Users;
use Igniter\User\Models\User;
use Illuminate\Http\RedirectResponse;

it('generates a login qr from the account page', function(): void {
    $user = User::factory()->superUser()->create();

    $this->actingAs($user, 'igniter-admin')
        ->post(route('igniter.user.users', ['slug' => 'account']), [], [
            'X-Requested-With' => 'XMLHttpRequest',
            'X-IGNITER-REQUEST-HANDLER' => 'onRegenerateLoginCode',
        ]);

    expect(LoginCode::query()->where('user_id', $user->getKey())->whereNull('used_at')->count())->toBe(1);

    $this->actingAs($user, 'igniter-admin')
        ->get(route('igniter.user.users', ['slug' => 'account']))
        ->assertOk()
        ->assertSee(lang('igniter.api::default.qr_login.button_regenerate'), false)
        ->assertSee('orderpoint-login-qr', false);
});

it('shows generate button on account page without an existing login code', function(): void {
    $user = User::factory()->superUser()->create();

    $this->actingAs($user, 'igniter-admin')
        ->get(route('igniter.user.users', ['slug' => 'account']))
        ->assertOk()
        ->assertSee(lang('igniter.api::default.qr_login.button_generate'), false)
        ->assertDontSee('orderpoint-login-qr', false);
});

it('does not add the login qr field when editing a staff user', function(): void {
    $user = User::factory()->superUser()->create();

    actingAsSuperUser()
        ->get(route('igniter.user.users', ['slug' => 'edit/'.$user->getKey()]))
        ->assertOk()
        ->assertDontSee(lang('igniter.api::default.qr_login.label'), false);
});

it('does not generate a login code without a staff user', function(): void {
    $response = (new Users)->account_onRegenerateLoginCode();

    expect($response)->toBeInstanceOf(RedirectResponse::class)
        ->and(LoginCode::query()->count())->toBe(0);
});
