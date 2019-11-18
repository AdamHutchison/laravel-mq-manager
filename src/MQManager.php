<?php

namespace MQManager;


use Closure;

class MQManager
{
    protected $mqService;
    protected $registeredQueues = [];

    /**
     * MQManager constructor.
     */
    public function __construct()
    {
        $this->mqService = app(MQServiceStrategyFactory::class)
            ->getStrategyInstance(config('mqmanager.mq_service'));
    }

    /**
     * @param $message
     * @param null $queue
     * @return $this
     */
    public function sendMessage($message, $queue = null)
    {
        $this->mqService->sendMessage($message, $queue);
        return $this;
    }

    /**
     * @param null $queue
     */
    public function listen($queue = null)
    {
        if ($queue) {
            $this->mqService->listen($queue);
        } else {
            $this->mqService->listen($this->registeredQueues);
        }
    }

    /**
     * @param $queueName
     * @param Closure $closure
     * @return $this
     */
    public function registerQueueListener($queueName, Closure $closure)
    {
        $this->registeredQueues[$queueName] = $closure;
        return $this;
    }
}
