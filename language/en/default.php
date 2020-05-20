<?php

return [
    'search_prompt' => 'Search api name',
    'search_tokens_prompt' => 'Search tokens',
    
    'resources' => 'Resources list',
    'tokens' => 'Issued tokens',
    
    'api_name' => 'API Name',
    'base_endpoint' => 'Base Endpoint',
    'description' => 'Description',
    'api_name_comment' => 'Name of your API resource',
    'base_endpoint_comment' => 'Describe your API resource',
    'description_comment' => 'https://example.com/api/<b>endpoint</b>',
	'model' => 'Model',
	'controller' => 'Controller',
	'transformer' => 'Transformer',
	'allowed_actions' => 'Allowed Actions',
	'allowed_actions_comment' => 'Leave blank to deactivate the endpoint.',
	'require_authorization' => 'Require authorization',
	'require_authorization_comment' => 'Warning: actions without authorization will be publicly accessible',
	
	'actions' => [
        'index' => 'List all resources (GET)',
        'show' => 'Show a single resource (GET)',
        'store' => 'Create a resource (POST)',
        'update' => 'Update a resource (PUT/PATCH)',
        'destroy' => 'Delete a resource (DELETE)',
	],
	
	'issued_to' => 'Issued to',
	'token_type' => 'Type',
	'token_type_staff' => 'Staff',
	'token_type_customer' => 'Customer',
	'device_name' => 'Device name',
	'created' => 'Created on',
	'lastused' => 'Last used',
    
];
