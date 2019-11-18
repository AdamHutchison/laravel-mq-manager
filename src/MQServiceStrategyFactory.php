<?php


namespace MQManager;



use MQManager\MQServiceStrategies\RabbitMQ\RabbitMQStrategy;

class MQServiceStrategyFactory
{
    /**
     * @var array
     */
    protected $registeredStrategies = [
        'rabbitmq' => RabbitMQStrategy::class,
    ];

    /**
     * @param $key
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    public function getStrategyInstance($key)
    {
        if(array_key_exists($key, $this->registeredStrategies)){
            return app($this->registeredStrategies[$key]);
        }
    }

    /**
     * @param $key
     * @param $strategy
     */
    public function registerStrategy($key, $strategy)
    {
        $this->registeredStrategies[$key] = $strategy;
    }
}