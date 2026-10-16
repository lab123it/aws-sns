<?php

namespace Lab123\AwsSns\Exceptions;

class CouldNotSendNotification extends \Exception
{
    public static function serviceRespondedWithAnError(string $message = ''): static
    {
        return new static('Could not send message.'.$message);
    }
}
