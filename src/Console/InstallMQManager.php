<?php

namespace MQManager\Console;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use MQManager\MQManagerServiceProvider;

class InstallMQManager extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mq-manager:install';

    /**
     * The console command description.
     *test:routes
     * @var string
     */
    protected $description = 'Install MQManager';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->call('vendor:publish', [
            '--provider' => MQManagerServiceProvider::class,
        ]);

        $this->info('MQ Manager has successfully been installed');
    }
}