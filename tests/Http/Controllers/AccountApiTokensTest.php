<?php

declare(strict_types=1);

namespace Igniter\Api\Tests\Http\Controllers;

use Igniter\Api\Classes\ApiManager;
use Igniter\Api\Listeners\AccountSubscriber;
use Igniter\Api\Models\Resource;
use Igniter\Api\Models\Token;
use Igniter\User\Models\User;
use Igniter\User\Models\UserRole;

function accountTokenRequest(string $handler): array
{
    return ['X-Requested-With' => 'XMLHttpRequest', 'X-IGNITER-REQUEST-HANDLER' => $handler];
}

function staffWithApiPermission(bool $allowed = true): User
{
    $role = UserRole::factory()->create(['permissions' => $allowed ? ['Igniter.Api.Manage' => 1] : []]);

    return User::factory()->for($role, 'role')->create(['super_user' => false]);
}

it('lists an ability for each enabled staff action of each resource', function(): void {
    $abilities = resolve(ApiManager::class)->listTokenAbilities();

    expect($abilities)->toHaveKeys(['categories:index', 'categories:show', 'categories:store', 'categories:update', 'categories:destroy', 'orders:index'])
        ->and($abilities)->not->toHaveKey('*')
        ->and($abilities)->not->toHaveKey('categories:*')
        ->and($abilities['categories:index'])->toBe('Categories: '.lang('igniter.api::default.actions.text_index'));
});

it('leaves out customer-only actions', function(): void {
    // `customers:show` is registered as `show:admin:admin,customer`, so it may be set to customer-only.
    $resource = Resource::query()->where('endpoint', 'customers')->first();
    $meta = $resource->meta;
    $meta['authorization']['show'] = 'customer';
    $resource->meta = $meta;
    $resource->save();

    expect(resolve(ApiManager::class)->listTokenAbilities())->not->toHaveKey('customers:show')
        ->toHaveKey('customers:index');
});

it('keeps an action whose saved customer-only value is not allowed', function(): void {
    $resource = Resource::query()->where('endpoint', 'categories')->first();
    $meta = $resource->meta;
    $meta['authorization']['destroy'] = 'customer';
    $resource->meta = $meta;
    $resource->save();

    expect(resolve(ApiManager::class)->listTokenAbilities())->toHaveKey('categories:destroy');
});

it('leaves out resources whose controller class does not exist', function(): void {
    Resource::create(['name' => 'Unregistered', 'endpoint' => 'unregistered']);

    expect(array_keys(resolve(ApiManager::class)->listTokenAbilities()))
        ->not->toContain('unregistered:index')
        ->toContain('categories:index');
});

it('groups abilities by resource on the token form', function(): void {
    $groups = (new AccountSubscriber)->groupAbilities([
        'categories:index' => 'Categories: List all resources (GET)',
        'categories:store' => 'Categories: Create a resource (POST)',
        'orders:index' => 'Orders: List all resources (GET)',
    ]);

    expect($groups)->toBe([
        'Categories' => ['categories:index' => 'List all resources (GET)', 'categories:store' => 'Create a resource (POST)'],
        'Orders' => ['orders:index' => 'List all resources (GET)'],
    ]);
});

it('groups abilities by the ability prefix when the label has no resource name', function(): void {
    $groups = (new AccountSubscriber)->groupAbilities([
        'menus:index' => 'List all resources (GET)',
        'menus:show' => 'Retrieve a resource (GET)',
    ]);

    expect($groups)->toBe([
        'menus' => ['menus:index' => 'List all resources (GET)', 'menus:show' => 'Retrieve a resource (GET)'],
    ]);
});

it('shows the abilities grouped by resource', function(): void {
    $this->actingAs(staffWithApiPermission(), 'igniter-admin')
        ->get(route('igniter.user.users', ['slug' => 'account']))
        ->assertOk()
        ->assertSee('<div class="fw-bold mb-1">Categories</div>', false)
        ->assertSee('<code class="small">categories:index</code>', false);
});

it('lets a token with an action ability use only that action', function(): void {
    $user = staffWithApiPermission();
    $user->forceFill(['status' => true, 'is_activated' => true])->save();
    $token = Token::createToken($user, 'Reader', ['categories:index'])->plainTextToken;

    $this->withToken($token)->getJson(route('igniter.api.categories.index'))->assertOk();
    $this->withToken($token)->postJson(route('igniter.api.categories.store'), ['name' => 'New'])->assertForbidden();
});

it('shows the api tokens section to staff with the api permission', function(): void {
    $user = staffWithApiPermission();
    Token::createToken($user, 'Kitchen POS', ['orders:index']);

    $this->actingAs($user, 'igniter-admin')
        ->get(route('igniter.user.users', ['slug' => 'account']))
        ->assertOk()
        ->assertSee(lang('igniter.api::default.account_tokens.label'))
        ->assertSee('You have one API token.')
        ->assertSee('data-bs-target="#api-tokens-modal"', false)
        ->assertSee('data-bs-target="#api-token-create-modal"', false)
        ->assertSee('id="api-tokens-modal"', false)
        ->assertSee('id="api-token-create-modal"', false)
        ->assertSee('Kitchen POS')
        ->assertSee('onCreateApiToken', false)
        ->assertDontSee('data-api-tokens-open', false);
});

it('opens the tokens modal with the new token after creating one', function(): void {
    $this->actingAs(staffWithApiPermission(), 'igniter-admin')
        ->withSession([AccountSubscriber::NEW_TOKEN_SESSION_KEY => '1|plain-token-value'])
        ->get(route('igniter.user.users', ['slug' => 'account']))
        ->assertOk()
        ->assertSee('data-api-tokens-open', false)
        ->assertSee('1|plain-token-value');
});

it('hides the api tokens section from staff without the api permission', function(): void {
    $this->actingAs(staffWithApiPermission(false), 'igniter-admin')
        ->get(route('igniter.user.users', ['slug' => 'account']))
        ->assertOk()
        ->assertDontSee('onCreateApiToken', false);
});

it('creates a token with all or selected abilities and shows it once', function(): void {
    $user = staffWithApiPermission();

    $this->actingAs($user, 'igniter-admin')
        ->post(route('igniter.user.users', ['slug' => 'account']), [
            'api_token_name' => 'Till',
            'api_token_scope' => 'selected',
            'api_token_abilities' => ['orders:index', 'menus:show'],
        ], accountTokenRequest('onCreateApiToken'))
        ->assertSessionHas(AccountSubscriber::NEW_TOKEN_SESSION_KEY);

    $this->post(route('igniter.user.users', ['slug' => 'account']), [
        'api_token_name' => 'Everything',
        'api_token_scope' => 'all',
    ], accountTokenRequest('onCreateApiToken'));

    $tokens = Token::query()->where('tokenable_id', $user->getKey())->orderBy('id')->get();
    expect($tokens)->toHaveCount(2)
        ->and($tokens[0]->abilities)->toBe(['orders:index', 'menus:show'])
        ->and($tokens[1]->abilities)->toBe(['*']);
});

it('rejects unknown abilities', function(): void {
    $user = staffWithApiPermission();

    $this->actingAs($user, 'igniter-admin')
        ->post(route('igniter.user.users', ['slug' => 'account']), [
            'api_token_name' => 'Till',
            'api_token_scope' => 'selected',
            'api_token_abilities' => ['secrets:*'],
        ], accountTokenRequest('onCreateApiToken'));

    expect(Token::query()->where('tokenable_id', $user->getKey())->exists())->toBeFalse();
});

it('stops staff without the api permission creating tokens', function(): void {
    $user = staffWithApiPermission(false);

    $this->actingAs($user, 'igniter-admin')
        ->post(route('igniter.user.users', ['slug' => 'account']), [
            'api_token_name' => 'Till',
            'api_token_scope' => 'all',
        ], accountTokenRequest('onCreateApiToken'));

    expect(Token::query()->where('tokenable_id', $user->getKey())->exists())->toBeFalse();
});

it('only revokes your own tokens', function(): void {
    $user = staffWithApiPermission();
    $own = Token::createToken($user, 'Mine')->accessToken;
    $other = Token::createToken(staffWithApiPermission(), 'Theirs')->accessToken;

    $this->actingAs($user, 'igniter-admin')
        ->post(route('igniter.user.users', ['slug' => 'account']), ['token_id' => $other->getKey()], accountTokenRequest('onRevokeApiToken'));
    $this->post(route('igniter.user.users', ['slug' => 'account']), ['token_id' => $own->getKey()], accountTokenRequest('onRevokeApiToken'));

    expect(Token::query()->whereKey($other->getKey())->exists())->toBeTrue()
        ->and(Token::query()->whereKey($own->getKey())->exists())->toBeFalse();
});
