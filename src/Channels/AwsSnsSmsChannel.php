<?php
namespace Lab123\AwsSns\Channels;

use Lab123\AwsSns\Exceptions\CouldNotSendNotification;
use Illuminate\Notifications\Notification;
use Aws\Sns\SnsClient;

class AwsSnsSmsChannel
{

    public function __construct(SnsClient $client)
    {
        $this->client = $client;
    }

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable            
     * @param \Illuminate\Notifications\Notification $notification            
     *
     * @throws \Lab123\AwsSns\Exceptions\CouldNotSendNotification
     */
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toAwsSnsSms($notifiable);
        $message->phoneNumber = ($message->phoneNumber) ?: $notifiable->routeNotificationFor('AwsSnsSms');
        
        if (! $message->phoneNumber || ! $message->message) {
            return;
        }
        
        $this->configureSMS();
        
        $data = [
            'PhoneNumber' => $message->phoneNumber,
            'MessageStructure' => $message->messageStructure,
            'Message' => $message->message
        ];
        
        $response = $this->client->publish($data);
        
        $response = $response->toArray();
        
        if ($response["@metadata"]["statusCode"] != 200) {
            throw CouldNotSendNotification::serviceRespondedWithAnError();
        }
    }

    private function configureSMS()
    {
        $config = config('aws-sns');
        
        if (! $config || ! key_exists('sms', $config)) {
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
            'attributes' => $smsConfig
        ]);
    }
}