<div class="form-group">
    <p class="text-muted">@lang('igniter.api::default.qr_login.help_scan')</p>

    @if (!empty($orderPointLoginCode) && !empty($orderPointLoginQrData))
        <div class="d-flex flex-wrap align-items-start gap-3 mb-2">
            <div class="text-center">
                <img
                    id="orderpoint-login-qr"
                    class="border rounded bg-white p-2"
                    width="180"
                    height="180"
                    src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode($orderPointLoginQrData) }}"
                    alt="@lang('igniter.api::default.qr_login.label_qr')"
                >
            </div>
            <div class="flex-grow-1" style="min-width: 220px;">
                <div class="input-group mb-2">
                    <input type="text" class="form-control" readonly value="{{ $orderPointLoginCode }}" id="orderpoint-login-code">
                    <button
                        type="button"
                        class="btn btn-outline-secondary"
                        id="orderpoint-copy-login-code"
                        data-copy-label="@lang('igniter.api::default.qr_login.button_copy')"
                        data-copied-label="@lang('igniter.api::default.qr_login.button_copy_copied')"
                        data-flash-message="@lang('igniter.api::default.qr_login.alert_copied')"
                        data-flash-error="@lang('igniter.api::default.qr_login.alert_copy_failed')"
                    >
                        @lang('igniter.api::default.qr_login.button_copy')
                    </button>
                </div>
                <button
                    type="button"
                    class="btn btn-outline-secondary"
                    data-request="onRegenerateLoginCode"
                    data-request-flash=""
                    data-progress-indicator="@lang('admin::lang.text_loading')"
                >
                    @lang('igniter.api::default.qr_login.button_regenerate')
                </button>
            </div>
        </div>
    @else
        <button
            type="button"
            class="btn btn-outline-secondary"
            data-request="onRegenerateLoginCode"
            data-request-flash=""
            data-progress-indicator="@lang('admin::lang.text_loading')"
        >
            @lang('igniter.api::default.qr_login.button_generate')
        </button>
    @endif

    <script>
        (() => {
            const button = document.getElementById('orderpoint-copy-login-code');
            const input = document.getElementById('orderpoint-login-code');

            if (!button || !input) {
                return;
            }

            const showFlash = (message, level) => {
                if (typeof $ !== 'undefined' && $.ti && $.ti.flashMessage) {
                    $.ti.flashMessage({ text: message, class: level });
                }
            };

            const showCopiedFeedback = () => {
                showFlash(button.dataset.flashMessage, 'success');

                const copyLabel = button.dataset.copyLabel;
                const copiedLabel = button.dataset.copiedLabel;

                button.disabled = true;
                button.classList.remove('btn-outline-secondary');
                button.classList.add('btn-success');
                button.textContent = copiedLabel;

                window.setTimeout(() => {
                    button.disabled = false;
                    button.classList.add('btn-outline-secondary');
                    button.classList.remove('btn-success');
                    button.textContent = copyLabel;
                }, 2000);
            };

            const copyFallback = () => {
                input.focus();
                input.select();
                input.setSelectionRange(0, input.value.length);

                try {
                    if (document.execCommand('copy')) {
                        showCopiedFeedback();
                        return;
                    }
                } catch (error) {
                    // fall through to error feedback
                }

                showFlash(button.dataset.flashError, 'danger');
            };

            button.addEventListener('click', () => {
                const value = input.value;

                if (!value) {
                    return;
                }

                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(value).then(showCopiedFeedback).catch(copyFallback);
                    return;
                }

                copyFallback();
            });
        })();
    </script>
</div>
