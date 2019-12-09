<?php


namespace MQManager\MQServiceStrategies\RabbitMQ;


use Closure;
use MQManager\Exceptions\IncorrectQueueFormatException;
use MQManager\Interfaces\MQServiceInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQStrategy implements MQServiceInterface
{
    /**
     * @var AMQPStreamConnection
     */
    protected $connection;
    /**
     * @var \PhpAmqpLib\Channel\AMQPChannel
     */
    protected $channel;

    /**
     * RabbitMQStrategy constructor.
     */
    public function __construct()
    {
        $this->connection = new AMQPStreamConnection(
            config('mqmanager.host'),
            config('mqmanager.port'),
            config('mqmanager.username'),
            config('mqmanager.password')
        );

        $this->channel = $this->connection->channel();
    }

    /**
     *
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * @param $message
     * @param null $queue
     * @return mixed|void
     */
    public function sendMessage($message, $queue = null)
    {
        if (!$queue) {
            config('mqmanager.publish_queue');
        }

        $this->channel->queue_declare($queue);

        $msg = new AMQPMessage($message);

        $this->channel->basic_publish($msg, '', $queue);
    }

    /**
     * @param array $queues
     * @param Closure|null $closure
     * @return mixed|void
     * @throws IncorrectQueueFormatException
     * @throws \ErrorException
     */
    public function listen($queues = [], Closure $closure = null)
    {
        if (empty($queues)) {
            throw new IncorrectQueueFormatException('No queues have been registered');
        } elseif (is_string($queues)) {
            $this->channel->queue_declare($queues);
            $this->channel->basic_consume($queues, '', false, true, false, false, $closure);
        } elseif (is_array($queues)) {
            foreach ($queues as $queue => $closure) {
                $this->channel->queue_declare($queue);
                $this->channel->basic_consume($queue, '', false, true, false, false, $closure);
            }
        } else {
            $type = gettype($queues);
            throw new IncorrectQueueFormatException("parameter 1 passed to RabbitMQStrategy listen method must either be a string or array {$type} given");
        }

        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }
    }

    /**
     * @throws \Exception
     */
    public function close()
    {
        $this->channel->close();
        $this->connection->close();
    }
}
