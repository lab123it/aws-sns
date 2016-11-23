<?php
namespace Lab123\AwsSns\Exceptions;

class CouldNotSendNotification extends \Exception
{

    public static function serviceRespondedWithAnError($message = '')
    {
        return new static("Could not send message." . $message);
    }
}
