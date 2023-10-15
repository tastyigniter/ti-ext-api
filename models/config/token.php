<?php


namespace Igniter\Api\Models\Config;

use Igniter\Flame\Traits\EventEmitter;

class TokenConfig
{
    use EventEmitter;
    
    public function configureTokenList()
    {
        $columns = [
            'tokenable_id' => [
                'label' => 'lang:igniter.api::default.column_issued_to',
                'searchable' => true,
                'formatter' => function ($record, $column, $value) {
                    $value = $record->tokenable_type == 'users'
                        ? $record->tokenable->username : $record->tokenable->email;

                    return $value;
                },
            ],
            'tokenable_type' => [
                'label' => 'lang:igniter.api::default.column_token_type',
                'searchable' => true,
                'formatter' => function ($record, $column, $value) {
                    return $value == 'users' ? lang('igniter.api::default.text_token_type_staff') : lang('igniter.api::default.text_token_type_customer');
                },
            ],
            'name' => [
                'label' => 'lang:igniter.api::default.column_device_name',
                'searchable' => true,
            ],
            'created_at' => [
                'label' => 'lang:igniter.api::default.column_created',
                'type' => 'datetime',
            ],
            'last_used_at' => [
                'label' => 'lang:igniter.api::default.column_lastused',
                'type' => 'datetime',
            ],
        ];

        // Allow extension/manipulation of columns array via event 
        $eventResults = $this->fireSystemEvent('api.token.extendConfigColumns', [$columns]);

        if (is_array($eventResults) && $eventResults !== null) {
            $columns = array_merge($columns, $eventResults);
        }

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
                        'back' => ['label' => 'lang:admin::lang.button_icon_back', 'class' => 'btn btn-outline-secondary', 'href' => 'igniter/api/resources'],
                    ],
                ],
                'bulkActions' => [
                    'delete' => [
                        'label' => 'lang:admin::lang.button_delete',
                        'class' => 'btn btn-light text-danger',
                        'data-request-confirm' => 'lang:admin::lang.alert_warning_confirm',
                    ],
                ],
                'columns' => $columns,
                
            ],
        ];
    }
}

// Instantiate and use your TokenConfig class
$config = new TokenConfig;
return $config->configureTokenList();
