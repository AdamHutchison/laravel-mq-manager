<?php

// Settings default to RabbitMQ defaults
return [
    'host' => env('MQ_MANAGER_HOST', 'localhost'),
    'port' => env('MQ_MANAGER_PORT', '5672'),
    'username' => env('MQ_MANAGER_USERNAME', 'guest'),
    'password' => env('MQ_MANAGER_PASSWORD', 'guest'),
    'publish_queue' => env('MQ_MANAGER_QUEUE'),
    'mq_service' => env('MQ_MANAGER_SERVICE', 'rabbitmq'),
];
