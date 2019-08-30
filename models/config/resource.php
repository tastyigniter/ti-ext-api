<?php

return [
    'list' => [
        'filter' => [
            'search' => [
                'prompt' => 'Search api name',
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
                'name' => 'API Name',
                'searchable' => TRUE,
            ],
            'base_endpoint' => [
                'label' => 'Base Endpoint',
                'sortable' => FALSE,
            ],
            'description' => [
                'label' => 'Description',
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
                'label' => 'API Name',
                'type' => 'text',
                'span' => 'left',
                'comment' => 'Name of your API resource',
            ],
            'description' => [
                'label' => 'Short Description',
                'type' => 'text',
                'span' => 'right',
                'comment' => 'Describe your API resource',
            ],
            'endpoint' => [
                'label' => 'Base Endpoint',
                'type' => 'text',
                'span' => 'left',
                'comment' => 'https://example.com/api/<b>endpoint</b>',
                'commentHtml' => TRUE,
            ],
            'model' => [
                'label' => 'Model',
                'type' => 'text',
                'span' => 'right',
                'disabled' => TRUE,
            ],
            'controller' => [
                'label' => 'Controller',
                'type' => 'text',
                'span' => 'left',
                'disabled' => TRUE,
            ],
            'transformer' => [
                'label' => 'Transformer',
                'type' => 'text',
                'span' => 'right',
                'disabled' => TRUE,
            ],
        ],
    ],
];