# Message Queue Manager For Laravel Microservices

Message queue manager (MQ Manager) is a package that allows asynchronous inter service messages to be sent
by micro services built on the laravel framework. 

By default the MQ managers uses RabbitMQ as the message broker using the following connection credentials:

`host:localhost` `port:5672` `username:guest` `password:guest`

These can be be set manually via the following env values:

* `MQ_MANAGER_HOST`

* `MQ_MANAGER_PORT`

* `MQ_MANAGER_USERNAME`

* `MQ_MANAGER_PASSWORD`

## Installation

The package can then be installed using composer:
 
 `composer require adamhutchison/laravel-mq-manager`

Then run the following command:

`php artisan mq-manager:install`

This will publish the `mqmanager.php` config file to the applications config folder

Then set the following environment variables in the applications `.env` file:

* `MQ_MANAGER_QUEUE` - defines the default queue that you wish messages to be published to, this will default
to `queue`

* `MQ_MANAGER_SERVICE` - states the MQ service driver you wish to use to power your inter service
messaging, the package uses `rabbitmq`, we are looking to add ActiveMQ in the future. 

## Basic usage

### Sending Messages

By default MQ Manager will attempt to send messages to the queue defined in the `MQ_MANAGER_QUEUE` env variable,
however you may also pass a custom queue name as a second parameter to the `sendMessage()` method on the `MQManager` class. 

**It's important to know that the `MQManager` class is a singleton so it should be resolved using Laravels service container 
rather than using the new operator**

#### Basic Example:
```php 
    // Sends message 'Hello world to the queue deined by MQ_MANAGER_QUEUE env value
    app(MQManager::class)->sendMessage('Hello World');

    // Sends message to a custom queue
    app(MQManager::class)->sendMessage('Hello World', 'some_custom_queue');
```
### Receiving Messages

You can listen for messages sent by other services using the `listen()` method on the
`MQManager` class. By default MQ Manager will listen for messages sent to the queues registered
with the `registerQueueListener()` method on the `MQManager` class. This method expects two parameter, 
the first is the name of the queue and the second is a closure that that the message will be passed to. 

```php
    app(MQManager::class)->registerQueueListener('test_queue', function($message){
        echo $message->getBody();
    });
```

You may also pass in a custom queue and closure to the listen method. In this case only messages sent to the
specified queue will be listened for, all queues subscribed to in the the `mqmanager.php` config
will be ignored.

#### Basic Example
```php
    // Listen for messages on queues defined in mqconfig.subscribed_queues array
    app(MQManager::class)->listen();
    
    // listen for messages sent to a custom queue and handle using a callback
    app(MQManager::class)->listen('custom_queue', function($message){
        echo $message->body;
    });
```

## Automatic Messaging For Models Events

MQ Manager includes functionality that allows messages to be automatically sent to the
when a model is created, updated or deleted. Simply add the `MQManager\Events\ModelTraits\SendsMQMessages`
to any model that you wish messages to be broadcast for. 

```php
    <?php
    
    namespace App;
    
    use Illuminate\Database\Eloquent\Model;
    use MQManager\ModelTraits\ModelTraits\SendsMQMessages; 
    
    class Job extends Model
    {
        use SendsMQMessages;    
    }
```

MQ Messenger will then send a message in the following format to the queue defined in the .env file

```json
{
  "event_type": "created",
  "model": "App\\SomeModel",
  "data": {
      "id": 1,
      "model_field_1": "value",
      "some_other_model_field": "value2"
    }
}
```

## Listening For Model Events

MQ Manager provides the following artisan command for listening for model events `php artisan mq-manager:listen`.
This artisan command can be started and monitored using supervisor (in the same way that queue worker would) to allow for 
model events to be detected from a different microservice.

