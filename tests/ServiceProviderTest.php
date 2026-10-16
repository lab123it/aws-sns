<?php

namespace Lab123\AwsSns\Test;

use Aws\Sns\SnsClient;
use Lab123\AwsSns\Channels\AwsSnsSmsChannel;
use Lab123\AwsSns\Channels\AwsSnsTopicChannel;

class ServiceProviderTest extends TestCase
{
    protected function defineEnvironment($app): void
    {
        $app['config']->set('services.sns', ['key' => 'k', 'secret' => 's', 'region' => 'eu-west-2']);
    }

    public function test_channels_get_an_sns_client_built_from_services_config(): void
    {
        foreach ([AwsSnsSmsChannel::class, AwsSnsTopicChannel::class] as $class) {
            $channel = $this->app->make($class);
            $client = (fn () => $this->client)->call($channel);

            $this->assertInstanceOf(SnsClient::class, $client);
            $this->assertSame('eu-west-2', $client->getRegion());
        }
    }

    public function test_sms_defaults_config_is_available(): void
    {
        $this->assertArrayHasKey('monthlySpendLimit', config('aws-sns.sms'));
    }
}
