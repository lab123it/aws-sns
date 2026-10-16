<?php

namespace Lab123\AwsSns;

use Aws\Sns\SnsClient;
use Illuminate\Support\ServiceProvider;
use Lab123\AwsSns\Channels\AwsSnsSmsChannel;
use Lab123\AwsSns\Channels\AwsSnsTopicChannel;

class AwsSnsServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/Config/aws-sns.php', 'aws-sns');

        foreach ([AwsSnsSmsChannel::class, AwsSnsTopicChannel::class] as $channel) {
            $this->app->when($channel)
                ->needs(SnsClient::class)
                ->give(fn () => $this->makeSnsClient());
        }
    }

    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/Config/aws-sns.php' => $this->app->configPath('aws-sns.php'),
        ], 'config');
    }

    /**
     * Build the SNS client from the `services.sns` configuration.
     */
    private function makeSnsClient(): SnsClient
    {
        $config = $this->app['config']->get('services.sns');

        return new SnsClient([
            'credentials' => [
                'key' => $config['key'],
                'secret' => $config['secret'],
            ],
            'version' => 'latest',
            'region' => $config['region'],
        ]);
    }
}
