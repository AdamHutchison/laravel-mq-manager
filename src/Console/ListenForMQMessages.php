<?php

namespace MQManager\Console;


use Illuminate\Console\Command;
use MQManager\Exceptions\IncorrectQueueFormatException;
use MQManager\MQManager;

class ListenForMQMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mq-manager:listen';

    /**
     * The console command description.
     *test:routes
     * @var string
     */
    protected $description = 'Listen for Async Messages From other micro-services using MQManager';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $mqManager = app(MQManager::class);

        try {
            $mqManager->listen();
        } catch (IncorrectQueueFormatException $e) {
            $this->info($e->getMessage() . ' - closing MQ connection');
            die();
        }

        $this->info('MQ Manager is listening for messages');
    }
}