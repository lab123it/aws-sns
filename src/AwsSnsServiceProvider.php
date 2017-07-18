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
    public function register()
    {
        
    }

    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->publishConfigs();
        $this->registerConfigs();
        $this->bindingSnsClient();
    }

    private function bindingSnsClient()
    {
        $this->app->when(AwsSnsSmsChannel::class)
            ->needs(SnsClient::class)
            ->give(function () {
            return $this->giveSnsInstance();
        });
        
        $this->app->when(AwsSnsTopicChannel::class)
            ->needs(SnsClient::class)
            ->give(function () {
            return $this->giveSnsInstance();
        });
    }

    private function giveSnsInstance()
    {
        $config = config('services.sns');
        
        return SnsClient::factory([
            'credentials' => [
                'key' => $config['key'],
                'secret' => $config['secret']
            ],
            'version' => 'latest',
            'region' => $config['region']
        ]);
    }

    /**
     * Publish configs.
     *
     * @return void
     */
    protected function publishConfigs()
    {
        $this->publishes([
            __DIR__ . '/Config/aws-sns.php' => config_path('aws-sns.php')
        ], 'config');
    }

    /**
     * Register configs.
     *
     * @return void
     */
    protected function registerConfigs()
    {
        config('aws-sns');
    }
}
