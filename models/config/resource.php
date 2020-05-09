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
                'delete' => ['label' => 'lang:admin::lang.button_delete', 'class' => 'btn btn-danger', 'data-request-form' => '#list-form', 'data-request' => 'onDelete', 'data-request-data' => "_method:'DELETE'", 'data-request-confirm' => 'lang:admin::lang.alert_warning_confirm'],
                'filter' => ['label' => 'lang:admin::lang.button_icon_filter', 'class' => 'btn btn-default btn-filter', 'data-toggle' => 'list-filter', 'data-target' => '.list-filter'],
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
                'name' => 'lang:igniter.api::default.api_name',
                'searchable' => TRUE,
            ],
            'base_endpoint' => [
                'label' => 'lang:igniter.api::default.base_endpoint',
                'sortable' => FALSE,
            ],
            'description' => [
                'label' => 'lang:igniter.api::default.description',
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
                'label' => 'lang:igniter.api::default.api_name',
                'type' => 'text',
                'span' => 'left',
                'comment' => 'lang:igniter.api::default.api_name_comment',
            ],
            'description' => [
                'label' => 'lang:igniter.api::default.base_endpoint',
                'type' => 'text',
                'span' => 'right',
                'comment' => 'lang:igniter.api::default.base_endpoint_comment',
            ],
            'endpoint' => [
                'label' => 'lang:igniter.api::default.description',
                'type' => 'text',
                'span' => 'left',
                'comment' => 'lang:igniter.api::default.description_comment',
                'commentHtml' => TRUE,
            ],
            'model' => [
                'label' => 'lang:igniter.api::default.model',
                'type' => 'text',
                'span' => 'right',
                'disabled' => TRUE,
            ],
            'controller' => [
                'label' => 'lang:igniter.api::default.controller',
                'type' => 'text',
                'span' => 'left',
                'disabled' => TRUE,
            ],
            'transformer' => [
                'label' => 'lang:igniter.api::default.transformer',
                'type' => 'text',
                'span' => 'right',
                'disabled' => TRUE,
            ],
            'meta[actions]' => [
                'label' => 'lang:igniter.api::default.allowed_actions',
                'type' => 'checkboxlist',
                'span' => 'left',
                'disabled' => FALSE,
                'default' => ['index', 'store', 'show', 'update', 'destroy'],
                'options' => [
                    'index' => 'lang:igniter.api::default.actions.index',
                    'show' => 'lang:igniter.api::default.actions.show',
                    'store' => 'lang:igniter.api::default.actions.store',
                    'update' => 'lang:igniter.api::default.actions.update',
                    'destroy' => 'lang:igniter.api::default.actions.destroy',
                ],
                'comment' => 'lang:igniter.api::default.allowed_actions_comment',
            ],
        ],
    ],
];