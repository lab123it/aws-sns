<?php

namespace Lab123\AwsSns\Test;

use Aws\CommandInterface;
use Aws\Middleware;
use Aws\MockHandler;
use Aws\Result;
use Aws\Sns\SnsClient;
use Lab123\AwsSns\AwsSnsServiceProvider;
use Lab123\AwsSns\Channels\AwsSnsSmsChannel;
use Lab123\AwsSns\Channels\AwsSnsTopicChannel;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    /** @var array<int, CommandInterface> */
    protected array $commands = [];

    protected function getPackageProviders($app): array
    {
        return [AwsSnsServiceProvider::class];
    }

    /**
     * Make both channels use an SNS client that answers with the given status codes.
     *
     * @param  int[]  $statusCodes
     */
    protected function fakeSns(array $statusCodes = [200]): void
    {
        $client = new SnsClient([
            'region' => 'us-east-1',
            'version' => 'latest',
            'credentials' => ['key' => 'k', 'secret' => 's'],
            'handler' => new MockHandler(array_map(
                fn (int $code) => new Result(['@metadata' => ['statusCode' => $code]]),
                $statusCodes
            )),
        ]);

        $client->getHandlerList()->appendSign(Middleware::tap(function (CommandInterface $command) {
            $this->commands[] = $command;
        }));

        foreach ([AwsSnsSmsChannel::class, AwsSnsTopicChannel::class] as $channel) {
            $this->app->when($channel)->needs(SnsClient::class)->give(fn () => $client);
        }
    }

    /**
     * @return array<int, string>
     */
    protected function sentCommandNames(): array
    {
        return array_map(fn (CommandInterface $command) => $command->getName(), $this->commands);
    }
}
