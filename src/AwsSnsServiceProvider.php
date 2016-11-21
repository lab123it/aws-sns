<?php
namespace Lab123\AwsSns;

use Lab123\AwsSns\Channels\AwsSnsTopicChannel;
use Lab123\AwsSns\Channels\AwsSnsSmsChannel;
use Illuminate\Support\ServiceProvider;
use Aws\Sns\SnsClient;

class AwsSnsServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     */
    public function boot()
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
     * Register the application services.
     */
    public function register()
    {}
}