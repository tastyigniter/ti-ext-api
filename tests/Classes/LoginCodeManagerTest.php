<?php

declare(strict_types=1);

namespace Igniter\Api\Tests\Classes;

use Igniter\Api\Classes\LoginCodeManager;
use Igniter\Api\Models\LoginCode;
use Igniter\Api\Models\Token;
use Igniter\Flame\Exception\ApplicationException;
use Igniter\User\Models\User;

it('generates a hashed single-use login code', function(): void {
    $user = User::factory()->superUser()->create();
    $manager = resolve(LoginCodeManager::class);

    $plain = $manager->generate($user);

    $row = LoginCode::query()->where('user_id', $user->getKey())->first();

    expect($plain)->toHaveLength(48)
        ->and($row)->not->toBeNull()
        ->and($row->code_hash)->toBe($manager->hash($plain))
        ->and($row->code_hash)->not->toBe($plain)
        ->and($row->isUsed())->toBeFalse()
        ->and($row->user->is($user))->toBeTrue()
        ->and($manager->sessionKey($user))->toBe($manager->sessionKey((int)$user->getKey()))
        ->and($manager->qrPayload('plain-code'))->toBe(url('/api/orderpoint/login').'?code=plain-code');
});

it('redeems a login code into a token with requested abilities', function(): void {
    $user = User::factory()->superUser()->create([
        'email' => 'staff@example.com',
        'is_activated' => true,
    ]);
    $manager = resolve(LoginCodeManager::class);
    $plain = $manager->generate($user);

    $result = $manager->redeem($plain, 'tasty-mobile-ios', ['orders:*', 'menus:*']);

    expect($result['email'])->toBe('staff@example.com')
        ->and($result['token'])->toContain('|');

    $row = LoginCode::query()->where('code_hash', $manager->hash($plain))->first();
    expect($row->isUsed())->toBeTrue();

    expect(Token::query()
        ->where('tokenable_type', $user->getMorphClass())
        ->where('tokenable_id', $user->getKey())
        ->exists())->toBeTrue();
});

it('rejects unknown and already used codes', function(): void {
    $user = User::factory()->superUser()->create(['is_activated' => true]);
    $manager = resolve(LoginCodeManager::class);
    $plain = $manager->generate($user);

    $manager->redeem($plain, 'device', ['orders:*']);

    expect(fn(): array => $manager->redeem($plain, 'device', ['orders:*']))
        ->toThrow(ApplicationException::class, lang('igniter.api::default.qr_login.alert_used'));

    expect(fn(): array => $manager->redeem('not-a-real-code', 'device', ['orders:*']))
        ->toThrow(ApplicationException::class, lang('igniter.api::default.qr_login.alert_invalid'));
});

it('rejects a code when the staff user is missing', function(): void {
    $user = User::factory()->superUser()->create(['is_activated' => true]);
    $manager = resolve(LoginCodeManager::class);
    $plain = $manager->generate($user);
    $user->delete();

    expect(fn(): array => $manager->redeem($plain, 'device', ['orders:*']))
        ->toThrow(ApplicationException::class, lang('igniter.api::default.qr_login.alert_user_missing'));
});

it('rejects a code when the staff user is inactive', function(): void {
    $user = User::factory()->create(['is_activated' => false, 'status' => true]);
    $manager = resolve(LoginCodeManager::class);
    $plain = $manager->generate($user);

    expect(fn(): array => $manager->redeem($plain, 'device', ['orders:*']))
        ->toThrow(ApplicationException::class, lang('igniter.api::default.qr_login.alert_user_inactive'));
});

it('invalidates previous unused codes when regenerating', function(): void {
    $user = User::factory()->superUser()->create(['is_activated' => true]);
    $manager = resolve(LoginCodeManager::class);

    $first = $manager->generate($user);
    $second = $manager->generate($user);

    expect(LoginCode::query()->where('user_id', $user->getKey())->whereNull('used_at')->count())->toBe(1);

    expect(fn(): array => $manager->redeem($first, 'device', ['orders:*']))
        ->toThrow(ApplicationException::class, lang('igniter.api::default.qr_login.alert_invalid'));

    $result = $manager->redeem($second, 'device', ['orders:*']);
    expect($result['token'])->toContain('|');
});

it('exchanges a login code over http', function(): void {
    $user = User::factory()->superUser()->create([
        'email' => 'qr@example.com',
        'is_activated' => true,
    ]);
    $plain = resolve(LoginCodeManager::class)->generate($user);

    $this->post(route('igniter.api.orderpoint.login'), [
        'code' => $plain,
        'device_name' => 'tasty-mobile-ios',
        'abilities' => ['orders:*', 'app_settings:*', 'menus:*'],
    ])->assertCreated()
        ->assertJsonStructure(['status_code', 'token', 'email'])
        ->assertJsonPath('email', 'qr@example.com');
});

it('returns the specific failure message for an invalid login code over http', function(): void {
    $this->post(route('igniter.api.orderpoint.login'), [
        'code' => 'invalid',
        'device_name' => 'tasty-mobile-ios',
        'abilities' => ['orders:*'],
    ])->assertUnauthorized()
        ->assertJsonPath('message', lang('igniter.api::default.qr_login.alert_invalid'));
});

it('returns the specific failure message for a used login code over http', function(): void {
    $user = User::factory()->superUser()->create(['is_activated' => true]);
    $manager = resolve(LoginCodeManager::class);
    $plain = $manager->generate($user);
    $manager->redeem($plain, 'device', ['orders:*']);

    $this->post(route('igniter.api.orderpoint.login'), [
        'code' => $plain,
        'device_name' => 'tasty-mobile-ios',
        'abilities' => ['orders:*'],
    ])->assertUnauthorized()
        ->assertJsonPath('message', lang('igniter.api::default.qr_login.alert_used'));
});
