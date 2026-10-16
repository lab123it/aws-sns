<?php

namespace Lab123\AwsSns\Channels;

use Aws\Sns\SnsClient;
use Illuminate\Notifications\Notification;
use Lab123\AwsSns\Exceptions\CouldNotSendNotification;

class AwsSnsTopicChannel
{
    public function __construct(protected SnsClient $client)
    {
    }

    /**
     * Send the given notification.
     *
     * @throws \Lab123\AwsSns\Exceptions\CouldNotSendNotification
     */
    public function send(mixed $notifiable, Notification $notification): void
    {
        $message = $notification->toAwsSnsTopic($notifiable);
        $message->topicArn = ($message->topicArn) ?: $notifiable->routeNotificationFor('AwsSnsTopic');

        if (! $message->topicArn || ! $message->message) {
            return;
        }

        $response = $this->client->publish([
            'TopicArn' => $message->topicArn,
            'MessageStructure' => $message->messageStructure,
            'Message' => $message->message,
        ])->toArray();

        if ($response['@metadata']['statusCode'] != 200) {
            throw CouldNotSendNotification::serviceRespondedWithAnError();
        }
    }
}
