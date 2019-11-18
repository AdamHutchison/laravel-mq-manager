<?php


namespace MQManager\ModelTraits\ModelTraits;


use MQManager\Events\ModelEvents\ModelCreated;
use MQManager\Events\ModelEvents\ModelDeleted;
use MQManager\Events\ModelEvents\ModelUpdated;

trait SendsModelEventMessages
{
    public static function bootSendsModelEventMessages()
    {
        self::created(function ($model) {
            event(new ModelCreated($model));
        });

        self::updated(function ($model) {
            event(new ModelUpdated($model));
        });

        self::deleted(function ($model) {
            event(new ModelDeleted($model));
        });
    }

}