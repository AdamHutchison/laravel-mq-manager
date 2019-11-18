<?php


namespace MQManager;


use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use MQManager\Console\InstallMQManager;
use MQManager\Console\ListenForMQMessages;
use MQManager\Events\Listeners\SendModelChangedMQMessage;
use MQManager\Events\ModelEvents\ModelCreated;
use MQManager\Events\ModelEvents\ModelDeleted;
use MQManager\Events\ModelEvents\ModelUpdated;

class MQManagerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/Config/mqmanager.php' => config_path('mqmanager.php'),
        ]);

        $this->registerArtisanCommands();

        Event::listen([
            ModelCreated::class,
            ModelUpdated::class,
            ModelDeleted::class,
        ], SendModelChangedMQMessage::class);
    }

    public function register()
    {
        $this->app->singleton(MQServiceStrategyFactory::class, function () {
            return new MQServiceStrategyFactory();
        });

        $this->app->singleton(MQManager::class, function () {
            return new MQManager();
        });
    }

    private function registerArtisanCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands(array(
                InstallMQManager::class,
                ListenForMQMessages::class
            ));
        }
    }
}