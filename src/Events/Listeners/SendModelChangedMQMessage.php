<?php


namespace MQManager\Events\Listeners;


use MQManager\Events\ModelEvents\AbstractModelEvent;
use MQManager\MQManager;

class SendModelChangedMQMessage
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(AbstractModelEvent $event)
    {
        $model = $event->model;

        $data = [
            'event_type' => $event->type,
            'class' => get_class($model),
            'data' => $model->toArray(),
        ];

        app(MQManager::class)->sendMessage(json_encode($data), $event->queue);
    }
}