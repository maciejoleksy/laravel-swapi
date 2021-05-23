<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class ListUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List registered users';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $users   = User::all();
        $headers = [
            'Id',
            'Email',
            'Hero',
        ];

        $users = $users->map(function ($user) {
            return [
                $user->id,
                $user->email,
                $user->hero,
            ];
        });

        $this->table($headers, $users);
    }
}
