<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestUserQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'userqueue:send
    {--u|user= : User name}
    {--e|email= : User email}
    {--l|location= : User location}
    {--reply_queue= : Reply queue name}
    {--reply_exchange= : Reply exchange name}
    {--a|action=add_user : Action name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send data to user queue';

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
     * @return mixed
     */
    public function handle()
    {
        publish('user.add', [
            'action'   => $this->option('action'),
            'name'     => $this->option('user'),
            'email'    => $this->option('email'),
            'location' => $this->option('location'),
            'reply_to' => [
                'queue'    => $this->option('reply_queue'),
                'exchange' => $this->option('reply_exchange')
            ]
        ]);
    }
}
