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
                'create' => ['label' => 'lang:admin::lang.button_new', 'class' => 'btn btn-primary', 'href' => 'igniter/api/resources/create'],
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
            'endpoint' => [
                'label' => 'Base Endpoint'
            ],
        ],
    ],
    'form' => [
        'toolbar' => [
            'buttons' => [
                'save' => ['label' => 'lang:admin::lang.button_save', 'class' => 'btn btn-primary', 'data-request-submit' => 'true', 'data-request' => 'onSave'],
                'saveClose' => [
                    'label' => 'lang:admin::lang.button_save_close',
                    'class' => 'btn btn-default',
                    'data-request' => 'onSave',
                    'data-request-submit' => 'true',
                    'data-request-data' => 'close:1',
                ],
                'delete' => [
                    'label' => 'lang:admin::lang.button_icon_delete', 'class' => 'btn btn-danger',
                    'data-request-submit' => 'true', 'data-request' => 'onDelete', 'data-request-data' => "_method:'DELETE'",
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
                'type' => 'select',
                'span' => 'right',
                'comment' => 'Select the model to link with this API resource',
            ],
            'meta[actions]' => [
                'label' => 'Actions',
                'type' => 'checkbox',
                'span' => 'left',
                'default' => ['index', 'store', 'show', 'update', 'destroy'],
                'options' => [
                    'index' => 'Index',
                    'store' => 'Store',
                    'show' => 'Show',
                    'update' => 'Update',
                    'destroy' => 'Destroy',
                ],
                'comment' => 'Choose the actions handled by this API resource',
            ],
            'meta[relations]' => [
                'label' => 'Relations',
                'type' => 'text',
                'span' => 'right',
                'comment' => 'Comma separated list of relations of the selected model',
            ],
//            'transformer_content' => [
//                'label' => 'Fractal Transformer',
//                'type' => 'codeeditor',
//                'mode' => 'php',
//                'commentAbove' => 'Learn more about <a target="_blank" href="https://fractal.thephpleague.com/transformers/">fractal transformers</a>',
//                'commentHtml' => TRUE,
//            ],
            'controller' => [
                'label' => 'Controller',
                'type' => 'text',
                'span' => 'left',
                'context' => ['edit'],
                'disabled' => TRUE,
            ],
            'transformer' => [
                'label' => 'Transformer',
                'type' => 'text',
                'span' => 'right',
                'context' => ['edit'],
                'disabled' => TRUE,
            ],
        ]
    ],
];