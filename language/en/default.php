<?php

return [
    'search_prompt' => 'Search api name',
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
	
	'actions' => [
        'index' => 'List all resources (GET)',
        'show' => 'Show a single resource (GET)',
        'store' => 'Create a resource (POST)',
        'update' => 'Update a resource (PUT/PATCH)',
        'destroy' => 'Delete a resource (DELETE)',
	]
    
];
