<div class="form-group" data-api-tokens>
    <p class="text-muted mb-2" data-api-tokens-summary>
        {{ $apiTokens->isEmpty()
            ? lang('igniter.api::default.account_tokens.text_no_tokens')
            : trans_choice('igniter.api::default.account_tokens.text_token_count', $apiTokens->count(), ['count' => $apiTokens->count()]) }}
    </p>
    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#api-tokens-modal">
        @lang('igniter.api::default.account_tokens.button_view')
    </button>
    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#api-token-create-modal">
        @lang('igniter.api::default.account_tokens.button_create')
    </button>

    <div
        class="modal fade"
        id="api-tokens-modal"
        tabindex="-1"
        aria-labelledby="api-tokens-modal-title"
        aria-hidden="true"
        @if (!empty($apiNewToken)) data-api-tokens-open @endif
    >
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="api-tokens-modal-title">@lang('igniter.api::default.account_tokens.label')</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="@lang('igniter::admin.button_close')"></button>
                </div>
                <div class="modal-body">
                    @if (!empty($apiNewToken))
                        <div class="alert alert-success" data-api-new-token>
                            <p class="mb-2">@lang('igniter.api::default.account_tokens.text_new_token')</p>
                            <input type="text" class="form-control font-monospace" readonly value="{{ $apiNewToken }}" onclick="this.select()">
                        </div>
                    @endif

                    @if ($apiTokens->isEmpty())
                        <p class="text-muted mb-0">@lang('igniter.api::default.account_tokens.text_no_tokens')</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead>
                                <tr>
                                    <th>@lang('igniter.api::default.account_tokens.column_name')</th>
                                    <th>@lang('igniter.api::default.account_tokens.column_abilities')</th>
                                    <th>@lang('igniter.api::default.account_tokens.column_last_used')</th>
                                    <th>@lang('igniter.api::default.account_tokens.column_created')</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($apiTokens as $token)
                                    <tr data-api-token="{{ $token->getKey() }}">
                                        <td>{{ $token->name }}</td>
                                        <td>
                                            {{ in_array('*', (array)$token->abilities, true)
                                                ? lang('igniter.api::default.account_tokens.text_all_abilities')
                                                : implode(', ', array_map(fn($ability) => $apiTokenAbilities[$ability] ?? $ability, (array)$token->abilities)) }}
                                        </td>
                                        <td>{{ $token->last_used_at ? $token->last_used_at->isoFormat(lang('system::lang.moment.date_time_format')) : lang('igniter.api::default.account_tokens.text_never_used') }}</td>
                                        <td>{{ $token->created_at?->isoFormat(lang('system::lang.moment.date_format')) }}</td>
                                        <td class="text-end">
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-danger"
                                                data-request="onRevokeApiToken"
                                                data-request-data="token_id: {{ $token->getKey() }}"
                                                data-request-confirm="@lang('igniter.api::default.account_tokens.confirm_revoke')"
                                                data-progress-indicator="@lang('admin::lang.text_loading')"
                                            >@lang('igniter.api::default.account_tokens.button_revoke')</button>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">@lang('igniter::admin.button_close')</button>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#api-token-create-modal">
                        @lang('igniter.api::default.account_tokens.button_create')
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="api-token-create-modal" tabindex="-1" aria-labelledby="api-token-create-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="api-token-create-modal-title">@lang('igniter.api::default.account_tokens.text_create_title')</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="@lang('igniter::admin.button_close')"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" for="api-token-name">@lang('igniter.api::default.account_tokens.label_name')</label>
                        <input type="text" class="form-control" id="api-token-name" name="api_token_name" maxlength="255">
                    </div>
                    <div data-api-token-abilities>
                        @if ($apiTokenHasAllAbilities)
                            <p class="text-muted mb-0">@lang('igniter.api::default.account_tokens.text_super_user_abilities')</p>
                        @elseif (empty($apiTokenAbilityGroups))
                            <p class="text-muted mb-0">@lang('igniter.api::default.account_tokens.alert_no_abilities')</p>
                        @else
                            <p class="text-muted mb-2">@lang('igniter.api::default.account_tokens.text_permitted_abilities')</p>
                            <div class="row">
                                @foreach ($apiTokenAbilityGroups as $resourceName => $actions)
                                    <div class="col-sm-6 col-md-4 mb-2">
                                        <div class="fw-bold mb-1">{{ $resourceName }}</div>
                                        <ul class="list-unstyled mb-0">
                                            @foreach ($actions as $ability => $actionLabel)
                                                <li>{{ $actionLabel }} <code class="small">{{ $ability }}</code></li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">@lang('igniter.api::default.account_tokens.button_cancel')</button>
                    <button
                        type="button"
                        class="btn btn-primary"
                        @if (!$apiTokenHasAllAbilities && empty($apiTokenAbilityGroups)) disabled @endif
                        data-request="onCreateApiToken"
                        data-progress-indicator="@lang('admin::lang.text_loading')"
                    >@lang('igniter.api::default.account_tokens.button_create')</button>
                </div>
            </div>
        </div>
    </div>

    @if (!empty($apiNewToken))
        <script>
            // Show the new token, which is only shown once, as soon as the page loads.
            window.addEventListener('load', () => {
                const modal = document.querySelector('[data-api-tokens-open]');
                if (modal && window.bootstrap) {
                    window.bootstrap.Modal.getOrCreateInstance(modal).show();
                }
            });
        </script>
    @endif
</div>
