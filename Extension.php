<?php namespace Igniter\Api;

use Event;
use Igniter\Api\Exception\ExceptionHandler;
use System\Classes\BaseExtension;

/**
 * Api Extension Information File
 */
class Extension extends BaseExtension
{
    public function register()
    {
        $this->app->register(\Spatie\Fractal\FractalServiceProvider::class);
        $this->registerConsoleCommand('create.apiresource', \Igniter\Api\Console\CreateApiResource::class);

        $this->registerExceptionHandler();
    }

    public function boot()
    {
    }

    public function registerNavigation()
    {
        return [
            'tools' => [
                'child' => [
                    'resources' => [
                        'priority' => 9,
                        'class' => 'api-resources',
                        'href' => admin_url('igniter/api/resources'),
                        'title' => 'APIs',
                        'permission' => 'Igniter.Api',
                    ],
                ],
            ],
        ];
    }

    public function registerPermissions()
    {
        return [
            'Igniter.Api' => [
                'description' => 'Manges api resources',
                'action' => ['access', 'add', 'manage', 'delete'],
            ],
        ];
    }

    protected function registerExceptionHandler()
    {
        Event::listen('exception.beforeRender', function ($exception, $httpCode, $request) {
            $format = $this->app['config']->get('api.errorFormat');
            $handler = new ExceptionHandler($format);

            return $handler->handleException($exception);
        });
    }
}
