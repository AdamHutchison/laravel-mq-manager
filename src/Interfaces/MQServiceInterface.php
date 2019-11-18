<?php


namespace MQManager\Interfaces;


use Closure;

interface MQServiceInterface
{
    /**
     * @param $message
     * @param null $queue
     * @return mixed
     */
    public function sendMessage($message, $queue = null);

    /**
     * @param array $queues
     * @param Closure|null $closure
     * @return mixed
     */
    public function listen($queues = [], Closure $closure = null);
}