<?php

return [
    'list' => [
        'filter' => [
            'search' => [
                'prompt' => 'lang:igniter.api::default.search_prompt',
                'mode' => 'all',
            ],
        ],
        'toolbar' => [
            'buttons' => [
                'tokens' => ['label' => 'lang:igniter.api::default.button_tokens', 'class' => 'btn btn-primary', 'href' => 'igniter/api/tokens'],
                'delete' => ['label' => 'lang:admin::lang.button_delete', 'class' => 'btn btn-danger', 'data-request-form' => '#list-form', 'data-request' => 'onDelete', 'data-request-data' => "_method:'DELETE'", 'data-request-confirm' => 'lang:admin::lang.alert_warning_confirm'],
            ],
        ],
        'columns' => [
            'edit' => [
                'type' => 'button',
                'iconCssClass' => 'fa fa-pencil',
                'attributes' => [
                    'class' => 'btn btn-edit',
                    'href' => 'igniter/api/resources/edit/{id}',
                ],
            ],
            'name' => [
                'name' => 'lang:igniter.api::default.column_api_name',
                'searchable' => TRUE,
            ],
            'base_endpoint' => [
                'label' => 'lang:igniter.api::default.column_base_endpoint',
                'sortable' => FALSE,
            ],
            'description' => [
                'label' => 'lang:igniter.api::default.column_description',
            ],
        ],
    ],
    'form' => [
        'toolbar' => [
            'buttons' => [
                'save' => ['label' => 'lang:admin::lang.button_save', 'class' => 'btn btn-primary', 'data-request' => 'onSave'],
                'saveClose' => [
                    'label' => 'lang:admin::lang.button_save_close',
                    'class' => 'btn btn-default',
                    'data-request' => 'onSave',
                    'data-request-data' => 'close:1',
                ],
                'delete' => [
                    'label' => 'lang:admin::lang.button_icon_delete', 'class' => 'btn btn-danger',
                    'data-request' => 'onDelete', 'data-request-data' => "_method:'DELETE'",
                    'data-request-confirm' => 'lang:admin::lang.alert_warning_confirm', 'context' => ['edit'],
                ],
            ],
        ],
        'fields' => [
            'name' => [
                'label' => 'lang:igniter.api::default.label_api_name',
                'type' => 'text',
                'span' => 'left',
                'comment' => 'lang:igniter.api::default.label_api_name_comment',
            ],
            'description' => [
                'label' => 'lang:igniter.api::default.label_base_endpoint',
                'type' => 'text',
                'span' => 'right',
                'comment' => 'lang:igniter.api::default.label_base_endpoint_comment',
            ],
            'endpoint' => [
                'label' => 'lang:igniter.api::default.label_description',
                'type' => 'text',
                'span' => 'left',
                'comment' => 'lang:igniter.api::default.label_description_comment',
                'commentHtml' => TRUE,
            ],
            'model' => [
                'label' => 'lang:igniter.api::default.label_model',
                'type' => 'text',
                'span' => 'right',
                'disabled' => TRUE,
            ],
            'controller' => [
                'label' => 'lang:igniter.api::default.label_controller',
                'type' => 'text',
                'span' => 'left',
                'disabled' => TRUE,
            ],
            'transformer' => [
                'label' => 'lang:igniter.api::default.label_transformer',
                'type' => 'text',
                'span' => 'right',
                'disabled' => TRUE,
            ],
            'meta[actions]' => [
                'label' => 'lang:igniter.api::default.label_allowed_actions',
                'type' => 'checkboxlist',
                'span' => 'left',
                'disabled' => FALSE,
                'default' => ['index', 'store', 'show', 'update', 'destroy'],
                'options' => [
                    'index' => 'lang:igniter.api::default.actions.text_index',
                    'show' => 'lang:igniter.api::default.actions.text_show',
                    'store' => 'lang:igniter.api::default.actions.text_store',
                    'update' => 'lang:igniter.api::default.actions.text_update',
                    'destroy' => 'lang:igniter.api::default.actions.text_destroy',
                ],
                'comment' => 'lang:igniter.api::default.help_allowed_actions_comment',
            ],
            'meta[authorization]' => [
                'label' => 'lang:igniter.api::default.label_require_authorization',
                'type' => 'checkboxlist',
                'span' => 'right',
                'disabled' => FALSE,
                'default' => ['index', 'store', 'show', 'update', 'destroy'],
                'options' => [
                    'index' => 'lang:igniter.api::default.actions.text_index',
                    'show' => 'lang:igniter.api::default.actions.text_show',
                    'store' => 'lang:igniter.api::default.actions.text_store',
                    'update' => 'lang:igniter.api::default.actions.text_update',
                    'destroy' => 'lang:igniter.api::default.actions.text_destroy',
                ],
                'comment' => 'lang:igniter.api::default.help_require_authorization_comment',
            ],
        ],
    ],
];