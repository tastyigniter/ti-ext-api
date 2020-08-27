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
    protected $name = 'issue:token';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'TastyIgniter Issue API Access Tokens command.';

    /**
     * Execute the console command.
     * @return void
     */
    public function handle()
    {
        $user = null;
        if ($username = $this->option('username')) {
            $user = Users_model::where('username', $username)->first();
        }

        if ($email = $this->option('email')) {
            $user = Customers_model::where('email', $email)->first();
        }

        if (!$user) {
            $this->error('User does not exist!');

            return;
        }

        $accessToken = Token::createToken($user, $this->option('name'), ['*']);
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
