<?php

namespace Lab123\AwsSns\Channels;

use Aws\Sns\SnsClient;
use Illuminate\Notifications\Notification;
use Lab123\AwsSns\Exceptions\CouldNotSendNotification;

class AwsSnsSmsChannel
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
        $message = $notification->toAwsSnsSms($notifiable);
        $message->phoneNumber = ($message->phoneNumber) ?: $notifiable->routeNotificationFor('AwsSnsSms');

        if (! $message->phoneNumber || ! $message->message) {
            return;
        }

        $this->configureSMS();

        $response = $this->client->publish([
            'PhoneNumber' => $message->phoneNumber,
            'MessageStructure' => $message->messageStructure,
            'Message' => $message->message,
        ])->toArray();

        if ($response['@metadata']['statusCode'] != 200) {
            throw CouldNotSendNotification::serviceRespondedWithAnError();
        }
    }

    private function configureSMS(): void
    {
        $config = config('aws-sns');

        if (! $config || ! array_key_exists('sms', $config)) {
            return;
        }

        $smsConfig = [];
        foreach ($config['sms'] as $conf => $value) {
            if (! $value) {
                continue;
            }
            $smsConfig[ucfirst($conf)] = $value;
        }

        if (count($smsConfig) < 1) {
            return;
        }

        $this->client->setSMSAttributes([
            'attributes' => $smsConfig,
        ]);
    }
}
