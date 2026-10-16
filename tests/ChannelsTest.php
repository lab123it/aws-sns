<?php

namespace Lab123\AwsSns\Test;

use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Lab123\AwsSns\Channels\AwsSnsSmsChannel;
use Lab123\AwsSns\Channels\AwsSnsTopicChannel;
use Lab123\AwsSns\Exceptions\CouldNotSendNotification;
use Lab123\AwsSns\Messages\AwsSnsMessage;

class ChannelsTest extends TestCase
{
    private function notifiable(?string $phone = null, ?string $topic = null): object
    {
        return new class($phone, $topic)
        {
            use Notifiable;

            public function __construct(private ?string $phone, private ?string $topic) {}

            public function routeNotificationForAwsSnsSms(): ?string
            {
                return $this->phone;
            }

            public function routeNotificationForAwsSnsTopic(): ?string
            {
                return $this->topic;
            }
        };
    }

    private function smsNotification(AwsSnsMessage $message): Notification
    {
        return new class($message) extends Notification
        {
            public function __construct(private AwsSnsMessage $message) {}

            public function via($notifiable): array
            {
                return [AwsSnsSmsChannel::class];
            }

            public function toAwsSnsSms($notifiable): AwsSnsMessage
            {
                return $this->message;
            }
        };
    }

    private function topicNotification(AwsSnsMessage $message): Notification
    {
        return new class($message) extends Notification
        {
            public function __construct(private AwsSnsMessage $message) {}

            public function via($notifiable): array
            {
                return [AwsSnsTopicChannel::class];
            }

            public function toAwsSnsTopic($notifiable): AwsSnsMessage
            {
                return $this->message;
            }
        };
    }

    public function test_sms_is_published_to_the_message_phone_number(): void
    {
        $this->fakeSns();

        $this->notifiable()->notifyNow(
            $this->smsNotification((new AwsSnsMessage('Hi'))->phoneNumber('+5511999999999'))
        );

        $this->assertSame(['Publish'], $this->sentCommandNames());
        $this->assertSame('+5511999999999', $this->commands[0]['PhoneNumber']);
        $this->assertSame('Hi', $this->commands[0]['Message']);
        $this->assertSame('string', $this->commands[0]['MessageStructure']);
    }

    public function test_sms_falls_back_to_the_notifiable_route(): void
    {
        $this->fakeSns();

        $this->notifiable('+5511888888888')->notifyNow($this->smsNotification(new AwsSnsMessage('Hi')));

        $this->assertSame('+5511888888888', $this->commands[0]['PhoneNumber']);
    }

    public function test_sms_without_a_number_or_message_is_not_sent(): void
    {
        $this->fakeSns();

        $this->notifiable()->notifyNow($this->smsNotification(new AwsSnsMessage('Hi')));
        $this->notifiable('+5511888888888')->notifyNow($this->smsNotification(new AwsSnsMessage('')));

        $this->assertSame([], $this->commands);
    }

    public function test_sms_default_attributes_are_set_before_publishing(): void
    {
        $this->app['config']->set('aws-sns.sms', [
            'monthlySpendLimit' => '10',
            'defaultSMSType' => 'Transactional',
            'defaultSenderID' => null,
        ]);
        $this->fakeSns([200, 200]);

        $this->notifiable('+5511888888888')->notifyNow($this->smsNotification(new AwsSnsMessage('Hi')));

        $this->assertSame(['SetSMSAttributes', 'Publish'], $this->sentCommandNames());
        $this->assertSame(
            ['MonthlySpendLimit' => '10', 'DefaultSMSType' => 'Transactional'],
            $this->commands[0]['attributes']
        );
    }

    public function test_sms_error_response_throws(): void
    {
        $this->fakeSns([202]);

        $this->expectException(CouldNotSendNotification::class);

        $this->notifiable('+5511888888888')->notifyNow($this->smsNotification(new AwsSnsMessage('Hi')));
    }

    public function test_topic_is_published_to_the_message_topic_arn(): void
    {
        $this->fakeSns();

        $this->notifiable()->notifyNow(
            $this->topicNotification((new AwsSnsMessage('Hi'))->topicArn('arn:aws:sns:us-east-1:000000000000:topic'))
        );

        $this->assertSame(['Publish'], $this->sentCommandNames());
        $this->assertSame('arn:aws:sns:us-east-1:000000000000:topic', $this->commands[0]['TopicArn']);
        $this->assertSame('Hi', $this->commands[0]['Message']);
    }

    public function test_topic_falls_back_to_the_notifiable_route_and_skips_when_missing(): void
    {
        $this->fakeSns();

        $this->notifiable(null, 'arn:routed')->notifyNow($this->topicNotification(new AwsSnsMessage('Hi')));
        $this->notifiable()->notifyNow($this->topicNotification(new AwsSnsMessage('Hi')));

        $this->assertCount(1, $this->commands);
        $this->assertSame('arn:routed', $this->commands[0]['TopicArn']);
    }

    public function test_topic_error_response_throws(): void
    {
        $this->fakeSns([202]);

        $this->expectException(CouldNotSendNotification::class);

        $this->notifiable()->notifyNow(
            $this->topicNotification((new AwsSnsMessage('Hi'))->topicArn('arn:topic'))
        );
    }
}
