<?php

declare(strict_types=1);

namespace Igniter\Api\Classes;

use Igniter\Api\Models\LoginCode;
use Igniter\Api\Models\Token;
use Igniter\Flame\Exception\ApplicationException;
use Igniter\User\Models\User;
use Illuminate\Support\Str;

class LoginCodeManager
{
    public const string SESSION_KEY_PREFIX = 'igniter.api.login_code.';

    public function generate(User $user): string
    {
        LoginCode::query()
            ->where('user_id', $user->getKey())
            ->whereNull('used_at')
            ->delete();

        $plainCode = Str::random(48);

        LoginCode::query()->create([
            'user_id' => $user->getKey(),
            'code_hash' => $this->hash($plainCode),
        ]);

        return $plainCode;
    }

    /**
     * @param list<string> $abilities
     * @return array{token: string, email: string}
     */
    public function redeem(string $plainCode, string $deviceName, array $abilities): array
    {
        $loginCode = LoginCode::query()
            ->where('code_hash', $this->hash($plainCode))
            ->first();

        if (!$loginCode instanceof LoginCode) {
            throw new ApplicationException(lang('igniter.api::default.qr_login.alert_invalid'));
        }

        if ($loginCode->isUsed()) {
            throw new ApplicationException(lang('igniter.api::default.qr_login.alert_used'));
        }

        /** @var User|null $user */
        $user = User::query()->find($loginCode->user_id);
        if (!$user instanceof User) {
            throw new ApplicationException(lang('igniter.api::default.qr_login.alert_user_missing'));
        }

        if (!$user->is_activated) {
            throw new ApplicationException(lang('igniter.api::default.qr_login.alert_user_inactive'));
        }

        $loginCode->used_at = now();
        $loginCode->save();

        $token = Token::createToken($user, $deviceName, $abilities);

        return [
            'token' => $token->plainTextToken,
            'email' => (string)$user->email,
        ];
    }

    public function sessionKey(User|int $user): string
    {
        $userId = $user instanceof User ? $user->getKey() : $user;

        return self::SESSION_KEY_PREFIX.$userId;
    }

    public function hash(string $plainCode): string
    {
        return hash('sha256', $plainCode);
    }

    public function qrPayload(string $plainCode): string
    {
        $prefix = trim((string)config('igniter-api.prefix', 'api'), '/');

        return url('/'.$prefix.'/orderpoint/login').'?code='.urlencode($plainCode);
    }
}
