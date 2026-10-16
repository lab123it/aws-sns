<?php

namespace Lab123\AwsSns;

use Aws\Sns\SnsClient;
use Illuminate\Foundation\Application as FoundationApplication;
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
        $this->loadLumenConfigs();

        $this->mergeConfigFrom(__DIR__.'/Config/aws-sns.php', 'aws-sns');

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
            __DIR__.'/Config/aws-sns.php' => $this->configPath('aws-sns.php'),
        ], 'config');
    }

    /**
     * Lumen only reads a config file when asked to.
     *
     * Laravel's Application has an unrelated static configure(), so only
     * applications that are not Laravel's are treated as Lumen-style.
     */
    private function loadLumenConfigs(): void
    {
        if (! $this->app instanceof FoundationApplication && method_exists($this->app, 'configure')) {
            $this->app->configure('services');
            $this->app->configure('aws-sns');
        }
    }

    private function configPath(string $file): string
    {
        return $this->app instanceof FoundationApplication
            ? $this->app->configPath($file)
            : $this->app->basePath('config/'.$file);
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
