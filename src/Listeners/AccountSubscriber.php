<?php

declare(strict_types=1);

namespace Igniter\Api\Listeners;

use Igniter\Admin\Widgets\Form;
use Igniter\Api\Classes\ApiManager;
use Igniter\Api\Classes\LoginCodeManager;
use Igniter\Api\Models\Token;
use Igniter\Flame\Exception\FlashException;
use Igniter\User\Facades\AdminAuth;
use Igniter\User\Http\Controllers\Users as UsersController;
use Igniter\User\Models\User;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * Adds API features to the staff My account page: the OrderPoint login QR, and access
 * tokens that staff with the API permission create and revoke themselves.
 */
class AccountSubscriber
{
    public const string TOKENS_PERMISSION = 'Igniter.Api.Manage';

    public const string NEW_TOKEN_SESSION_KEY = 'igniter.api.account_new_token';

    public function subscribe(Dispatcher $events): void
    {
        UsersController::extendFormFields(function(Form $form, mixed $model, ?string $context): void {
            if ($context !== 'account' || !$model instanceof User) {
                return;
            }

            $this->addQrLoginField($form, $model);
            $this->addTokensField($form, $model);
        });

        UsersController::extend(function(UsersController $controller): void {
            $controller->addDynamicMethod('account_onRegenerateLoginCode', fn(): RedirectResponse => $this->onRegenerateLoginCode());
            $controller->addDynamicMethod('account_onCreateApiToken', fn(): RedirectResponse => $this->onCreateApiToken());
            $controller->addDynamicMethod('account_onRevokeApiToken', fn(): RedirectResponse => $this->onRevokeApiToken());
        });
    }

    //
    // OrderPoint login QR
    //

    public function addQrLoginField(Form $form, User $user): void
    {
        $loginCodes = resolve(LoginCodeManager::class);
        $plainCode = Session::get($loginCodes->sessionKey($user));

        $form->vars['orderPointLoginCode'] = $plainCode;
        $form->vars['orderPointLoginQrData'] = is_string($plainCode) && $plainCode !== ''
            ? $loginCodes->qrPayload($plainCode)
            : null;

        $form->addFields([
            'orderpoint_qr_login' => [
                'label' => 'lang:igniter.api::default.qr_login.label',
                'type' => 'partial',
                'path' => 'igniter.api::account.qr_login',
                'commentAbove' => 'lang:igniter.api::default.qr_login.help',
            ],
        ]);
    }

    public function onRegenerateLoginCode(): RedirectResponse
    {
        $user = AdminAuth::user();
        if (!$user instanceof User) {
            flash()->error(lang('igniter.api::default.qr_login.alert_failed'))->now();

            return Redirect::back();
        }

        $loginCodes = resolve(LoginCodeManager::class);
        $plainCode = $loginCodes->generate($user);
        Session::flash($loginCodes->sessionKey($user), $plainCode);
        flash()->success(lang('igniter.api::default.qr_login.alert_generated'))->now();

        return Redirect::back();
    }

    //
    // Access tokens
    //

    public function addTokensField(Form $form, User $user): void
    {
        if (!$user->hasPermission(self::TOKENS_PERMISSION)) {
            return;
        }

        $form->vars['apiTokens'] = Token::query()
            ->where('tokenable_type', $user->getMorphClass())
            ->where('tokenable_id', $user->getKey())
            ->orderByDesc('id')
            ->get();
        $apiManager = resolve(ApiManager::class);
        $form->vars['apiTokenAbilities'] = $apiManager->listTokenAbilities();
        $form->vars['apiTokenHasAllAbilities'] = $user->isSuperUser();
        $form->vars['apiTokenAbilityGroups'] = $this->groupAbilities($apiManager->listTokenAbilities($user));
        $form->vars['apiNewToken'] = Session::get(self::NEW_TOKEN_SESSION_KEY);

        $form->addFields([
            'api_tokens' => [
                'label' => 'lang:igniter.api::default.account_tokens.label',
                'type' => 'partial',
                'path' => 'igniter.api::account.api_tokens',
                'commentAbove' => 'lang:igniter.api::default.account_tokens.help',
            ],
        ]);
    }

    /**
     * Groups abilities by resource for the token form, from labels like "Categories: List all resources (GET)".
     *
     * @param array<string, string> $abilities
     * @return array<string, array<string, string>>
     */
    public function groupAbilities(array $abilities): array
    {
        $groups = [];
        foreach ($abilities as $ability => $label) {
            [$resourceName, $actionLabel] = str_contains($label, ': ')
                ? explode(': ', $label, 2)
                : [Str::before($ability, ':'), $label];

            $groups[$resourceName][$ability] = $actionLabel;
        }

        return $groups;
    }

    public function onCreateApiToken(): RedirectResponse
    {
        $user = $this->tokenManager();

        $data = Validator::make((array)post(), [
            'api_token_name' => ['required', 'string', 'max:255'],
        ], [], [
            'api_token_name' => lang('igniter.api::default.account_tokens.label_name'),
        ])->validate();

        $abilities = $this->abilitiesFor($user);
        throw_unless($abilities, new FlashException(lang('igniter.api::default.account_tokens.alert_no_abilities')));

        $token = Token::createToken($user, $data['api_token_name'], $abilities);

        Session::flash(self::NEW_TOKEN_SESSION_KEY, $token->plainTextToken);
        flash()->success(lang('igniter.api::default.account_tokens.alert_created'));

        return Redirect::back();
    }

    public function onRevokeApiToken(): RedirectResponse
    {
        $user = $this->tokenManager();

        $deleted = Token::query()
            ->where('tokenable_type', $user->getMorphClass())
            ->where('tokenable_id', $user->getKey())
            ->whereKey((int)post('token_id'))
            ->delete();

        $deleted
            ? flash()->success(lang('igniter.api::default.account_tokens.alert_revoked'))
            : flash()->error(lang('igniter.api::default.account_tokens.alert_not_found'));

        return Redirect::back();
    }

    /**
     * Abilities a new token gets: everything for a super user, otherwise the actions the
     * user's permissions allow, as registered on each API resource.
     *
     * @return list<string>
     */
    protected function abilitiesFor(User $user): array
    {
        return $user->isSuperUser() ? ['*'] : array_keys(resolve(ApiManager::class)->listTokenAbilities($user));
    }

    /**
     * The signed-in staff member, who must have the API permission to manage tokens.
     */
    protected function tokenManager(): User
    {
        $user = AdminAuth::user();
        throw_unless($user instanceof User && $user->hasPermission(self::TOKENS_PERMISSION),
            new FlashException(lang('igniter.api::default.account_tokens.alert_not_allowed')),
        );

        return $user;
    }
}
