<?php

return [
    'list' => [
        'filter' => [
            'search' => [
                'prompt' => 'lang:igniter.api::default.search_tokens_prompt',
                'mode' => 'all',
            ],
        ],
        'toolbar' => [
            'buttons' => [
                'back' => ['label' => 'lang:admin::lang.button_icon_back', 'class' => 'btn btn-default', 'href' => 'igniter/api/resources'],
                'delete' => ['label' => 'lang:admin::lang.button_delete', 'class' => 'btn btn-danger', 'data-request-form' => '#list-form', 'data-request' => 'onDelete', 'data-request-data' => "_method:'DELETE'", 'data-request-confirm' => 'lang:admin::lang.alert_warning_confirm'],
            ],
        ],
        'columns' => [
            'tokenable_id' => [
                'label' => 'lang:igniter.api::default.issued_to',
                'searchable' => TRUE,
                'formatter' => function ($record, $column, $value) {
                    $value = $record->tokenable_type == 'users'
                        ? $record->tokenable->username : $record->tokenable->email;

                    return $value;
                },
            ],
            'tokenable_type' => [
                'label' => 'lang:igniter.api::default.token_type',
                'searchable' => TRUE,
                'formatter' => function ($record, $column, $value) {
                    return $value == 'users' ? lang('igniter.api::default.token_type_staff') : lang('igniter.api::default.token_type_customer');
                },
            ],
            'name' => [
                'label' => 'lang:igniter.api::default.device_name',
                'searchable' => TRUE,
            ],
            'created_at' => [
                'label' => 'lang:igniter.api::default.created',
                'type' => 'datetime',
            ],
            'last_used_at' => [
                'label' => 'lang:igniter.api::default.lastused',
                'type' => 'datetime',
            ],
        ],
    ],
];