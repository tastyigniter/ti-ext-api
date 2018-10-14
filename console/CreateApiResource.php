<?php

namespace Igniter\Api\Console;

use Igniter\Flame\Scaffold\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class CreateApiResource extends GeneratorCommand
{
    /**
     * @var string The console command name.
     */
    protected $name = 'create:apiresource';

    /**
     * @var string The console command description.
     */
    protected $description = 'Creates a new API resource.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'API Controller & Transformer';

    /**
     * A mapping of stub to generated file.
     *
     * @var array
     */
    protected $stubs = [
        'controller.stub' => 'resources/{{studly_name}}.php',
        'transformer.stub' => 'resources/transformers/{{studly_singular_name}}Transformer.php',
    ];

    protected $defaultActions = [
        'index' => [
            'pageSize' => 20,
        ],
        'store' => [],
        'show' => [],
        'update' => [],
        'destroy' => [],
    ];

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function handle()
    {
        $this->prepareVars();

        $this->buildStubs();

        $this->info($this->type.' created successfully.');
    }

    /**
     * Prepare variables for stubs.
     *
     * return @array
     */
    protected function prepareVars()
    {
        $extensionCode = $this->argument('extension');
        $parts = explode('.', $extensionCode);
        $extension = array_pop($parts);
        $author = array_pop($parts);
        $controller = $this->argument('controller');
        $model = $this->option('model') ?? studly_case($author)
            .'\\'.studly_case($extension).'\\Models\\'.studly_case(str_singular($controller));

        $data = $this->option('meta');

        $this->vars = [
            'model' => $model,

            'relations' => $this->buildRelationsStub(array_get($data, 'relations', [])),

            'extension' => $extension,
            'lower_extension' => strtolower($extension),
            'title_extension' => title_case($extension),
            'studly_extension' => studly_case($extension),

            'author' => $author,
            'lower_author' => strtolower($author),
            'title_author' => title_case($author),
            'studly_author' => studly_case($author),

            'name' => $controller,
            'lower_name' => strtolower($controller),
            'title_name' => title_case($controller),
            'studly_name' => studly_case($controller),
            'singular_name' => str_singular($controller),
            'studly_singular_name' => studly_case(str_singular($controller)),
            'snake_singular_name' => snake_case(str_singular($controller)),
            'plural_name' => str_plural($controller),
            'lower_plural_name' => strtolower(str_plural($controller)),
            'studly_plural_name' => studly_case(str_plural($controller)),
            'snake_plural_name' => snake_case(str_plural($controller)),
        ];
    }

    protected function buildRelationsStub($relations)
    {
        if ($relations AND !is_array($relations))
            $relations = explode(',', $relations);

        $relations = is_array($relations) ? $relations : [];

        $stub = '';
        foreach ($relations as $relation) {
            $stub .= "'".$relation."',";
        }

        return $stub;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['extension', InputArgument::REQUIRED, 'The name of the extension where the controller is created. Eg: Igniter.Local'],
            ['controller', InputArgument::REQUIRED, 'The name of the controller. Eg: Menus'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Overwrite existing files with generated ones.'],
            ['model', null, InputOption::VALUE_OPTIONAL, 'Define which model name to use, otherwise the singular controller name is used.'],
            ['meta', null, InputOption::VALUE_OPTIONAL, 'Define which controller config values.'],
        ];
    }
}