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
                'delete' => ['label' => 'lang:admin::lang.button_delete', 'class' => 'btn btn-danger', 'data-request-form' => '#list-form', 'data-request' => 'onDelete', 'data-request-data' => "_method:'DELETE'", 'data-request-confirm' => 'lang:admin::lang.alert_warning_confirm'],
		        'tokens' => ['label' => 'lang:igniter.api::default.resources', 'class' => 'btn btn-default', 'href' => 'igniter/api/resources'],
                'filter' => ['label' => 'lang:admin::lang.button_icon_filter', 'class' => 'btn btn-default btn-filter', 'data-toggle' => 'list-filter', 'data-target' => '.list-filter'],
            ],
        ],
        'columns' => [
            'tokenable_type' => [
                'label' => 'lang:igniter.api::default.issued_to',
                'searchable' => TRUE,
                'formatter' => function($record, $column, $value){
	                
	                switch ($value){
		                
		                case 'customers':
		                	$me = new \Admin\Models\Customers_model();
		                	$newValue = $me::where(['customer_id' => $record->tokenable_id])->first();
		                	$value = $newValue->email;
		                break;
		                
		                default:
		                	$me = new \Admin\Models\Staff_model();
		                	$newValue = $me::where(['user_id' => $record->tokenable_id])->first();
		                	$value = $newValue->username;
		                break;
	                }
	                
	                return $value;
                }
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