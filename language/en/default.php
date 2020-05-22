<?php

return [
    'search_prompt' => 'Search api name',
    'search_tokens_prompt' => 'Search tokens',
    
    'button_tokens' => 'Issued tokens',
    
    'column_api_name' => 'API Name',
    'column_base_endpoint' => 'Base Endpoint',
    'column_description' => 'Description',
    
    'label_api_name' => 'API Name',
    'label_base_endpoint' => 'Base Endpoint',
    'label_description' => 'Description',
    'label_api_name_comment' => 'Name of your API resource',
    'label_base_endpoint_comment' => 'Describe your API resource',
    'label_description_comment' => 'https://example.com/api/<b>endpoint</b>',
    
	'label_model' => 'Model',
	'label_controller' => 'Controller',
	'label_transformer' => 'Transformer',
	'label_allowed_actions' => 'Allowed Actions',
	'label_allowed_actions_comment' => 'Leave blank to deactivate the endpoint.',
	'label_require_authorization' => 'Require authorization',
	'label_require_authorization_comment' => 'Warning: actions without authorization will be publicly accessible',
	
	'actions' => [
        'index' => 'List all resources (GET)',
        'show' => 'Show a single resource (GET)',
        'store' => 'Create a resource (POST)',
        'update' => 'Update a resource (PUT/PATCH)',
        'destroy' => 'Delete a resource (DELETE)',
	],
	
	'column_issued_to' => 'Issued to',
	'column_token_type' => 'Type',
	'column_token_type_staff' => 'Staff',
	'column_token_type_customer' => 'Customer',
	'column_device_name' => 'Device name',
	'column_created' => 'Created on',
	'column_lastused' => 'Last used',
    
];
