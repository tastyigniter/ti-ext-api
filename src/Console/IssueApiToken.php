<?php

namespace Igniter\Api\Console;

use Igniter\Api\Models\Token;
use Igniter\User\Models\Customer;
use Igniter\User\Models\User;
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
        $name = $this->option('name');
        $email = $this->option('email');

        if (!strlen($isForAdmin = $this->option('admin'))) {
            return $this->error('Missing --admin option');
        }

        $query = $isForAdmin ? User::query() : Customer::query();

        $query->where('email', $email);

        if (!$user = $query->first()) {
            return $this->error('User does not exist!');
        }

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
            ['email', null, InputOption::VALUE_REQUIRED, 'The email of the user you want to issue a token.'],
            ['admin', null, InputOption::VALUE_NONE, 'Specify to issue token for admin users'],
        ];
    }
}
