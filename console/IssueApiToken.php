<?php

namespace Igniter\Api\Console;

use Admin\Models\Customers_model;
use Admin\Models\Users_model;
use Igniter\Api\Models\Token;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class IssueApiToken extends Command
{
    /**
     * The console command name.
     * @var string
     */
    protected $name = 'api:token';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Issue API Access Tokens command.';

    /**
     * Execute the console command.
     * @return void
     */
    public function handle()
    {
        if (!strlen($name = $this->option('name')))
            return $this->error('Missing --name option');

        if (!strlen($username = $this->option('username'))
            && !strlen($email = $this->option('email'))
        ) {
            return $this->error('Missing --username OR --email option');
        }

        $user = $username
            ? Users_model::where('username', $username)->first()
            : Customers_model::where('email', $email)->first();

        if (!$user)
            return $this->error('User does not exist!');

        if (!strlen($name = $this->option('name')))
            return $this->error('Missing --name option');

        $accessToken = Token::createToken($user, $name, ['*']);
        $this->info(sprintf('Access Token: %s', $accessToken->plainTextToken));
    }

    /**
     * Get the console command options.
     */
    protected function getOptions()
    {
        return [
            ['name', null, InputOption::VALUE_REQUIRED, 'The name to identify the token.'],
            ['username', null, InputOption::VALUE_OPTIONAL, 'The username of the admin you want to issue a token.'],
            ['email', null, InputOption::VALUE_OPTIONAL, 'The email of the customer you want to issue a token.'],
        ];
    }
}
