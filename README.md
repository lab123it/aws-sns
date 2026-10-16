# Amazon SNS Notifications Channel for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/lab123/aws-sns.svg?style=flat-square)](https://packagist.org/packages/lab123/aws-sns)
[![tests](https://img.shields.io/github/actions/workflow/status/lab123it/aws-sns/tests.yml?branch=master&label=tests&style=flat-square)](https://github.com/lab123it/aws-sns/actions/workflows/tests.yml)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/lab123/aws-sns.svg?style=flat-square)](https://packagist.org/packages/lab123/aws-sns)

Send notifications through [Amazon SNS](https://aws.amazon.com/sns/) from Laravel or Lumen: SMS to a phone number or a message to a topic.

## Requirements

- PHP ^8.1
- Lumen 10 or 11, or Laravel 10, 11, 12 or 13 (`illuminate/*` ^10.0, ^11.0, ^12.60 or ^13.10)
- `aws/aws-sdk-php` ^3

Verified in real applications: Lumen 10.0 and 11.1, Laravel 10.50, 11.57, 12.70 and 13.37, sending SMS and topic messages against a local SNS endpoint.

Lumen 5 to 9 and Laravel 5 to 9 apps cannot use this release; stay on `lab123/aws-sns:^1.0` (the pre-2.0 code).

## Installation

```bash
composer require lab123/aws-sns
```

### Laravel 10 and 11, Lumen 10 and 11: security advisory

Composer blocks `illuminate/mail` 10 and 11 (a dependency of `illuminate/notifications`) because of advisory [PKSA-zwc5-qtrz-zm1n](https://packagist.org/security-advisories/PKSA-zwc5-qtrz-zm1n). Laravel 10 and 11 are end of life and **have no fix**; only `illuminate/*` ^12.60 and ^13.10 are patched. If you stay on 10 or 11 you accept that risk, and can install with:

```bash
composer config policy.advisories.ignore-id --json '["PKSA-zwc5-qtrz-zm1n"]'
composer require lab123/aws-sns
```

### Register the provider

Laravel: the service provider is auto-discovered.

Lumen (`bootstrap/app.php`), together with a notification setup such as [lab123/lumen-notification](https://packagist.org/packages/lab123/lumen-notification):

```php
$app->register(\Lab123\AwsSns\AwsSnsServiceProvider::class);
```

`AwsSnsLumenServiceProvider` from earlier versions still works as an alias.

### Configure the SNS credentials

Add an `sns` entry to `config/services.php`:

```php
'sns' => [
    'key' => env('SNS_KEY'),
    'secret' => env('SNS_SECRET'),
    'region' => env('SNS_REGION', 'us-east-1'),
],
```

```dotenv
SNS_KEY=YOUR_KEY
SNS_SECRET=YOUR_SECRET
SNS_REGION=YOUR_REGION
```

### Default SMS attributes (optional)

Publish the config and set the defaults that are applied through [SetSMSAttributes](https://docs.aws.amazon.com/sns/latest/api/API_SetSMSAttributes.html) before each SMS:

```bash
php artisan vendor:publish --provider="Lab123\AwsSns\AwsSnsServiceProvider" --tag=config
```

```php
// config/aws-sns.php
return [
    'sms' => [
        'monthlySpendLimit' => env('SNS_SMS_MONTHLY_LIMIT'),
        'deliveryStatusIAMRole' => env('SNS_SMS_DELIVERY_STATUS_IAM_ROLE'),
        'deliveryStatusSuccessSamplingRate' => env('SNS_SMS_DELIVERY_STATUS'),
        'defaultSenderID' => env('SNS_SMS_SENDER'),
        'defaultSMSType' => env('SNS_SMS_TYPE'),
        'usageReportS3Bucket' => env('SNS_SMS_REPORT_S3'),
    ],
];
```

Empty values are skipped.

## Usage

### Sending SMS

```php
use Lab123\AwsSns\Channels\AwsSnsSmsChannel;
use Lab123\AwsSns\Messages\AwsSnsMessage;

class Welcome extends Notification
{
    public function via(object $notifiable): array
    {
        return [AwsSnsSmsChannel::class];
    }

    public function toAwsSnsSms(object $notifiable): AwsSnsMessage
    {
        return (new AwsSnsMessage())->message('Message Here')->phoneNumber('+5511999999999');
    }
}
```

Omit `->phoneNumber()` and add a route method on the notifiable instead:

```php
public function routeNotificationForAwsSnsSms(): string
{
    return $this->phone_number;
}
```

Numbers use the international [E.164](https://en.wikipedia.org/wiki/E.164) format, e.g. `+5511999999999`.

### Sending to a topic

```php
use Lab123\AwsSns\Channels\AwsSnsTopicChannel;

public function via(object $notifiable): array
{
    return [AwsSnsTopicChannel::class];
}

public function toAwsSnsTopic(object $notifiable): AwsSnsMessage
{
    return (new AwsSnsMessage())
        ->message('Message Here')
        ->topicArn('arn:aws:sns:us-east-1:000000000000:name-topic');
}
```

Or provide the ARN from the notifiable:

```php
public function routeNotificationForAwsSnsTopic(): string
{
    return $this->topicArn;
}
```

### Available message methods

- `topicArn($topicArn)`: topic ARN from Amazon SNS
- `phoneNumber($phoneNumber)`: phone number to send the notification to
- `message($message)`: message content

A notification is skipped when there is no destination or no message, and a `CouldNotSendNotification` exception is thrown when SNS answers with a status other than 200.

## Changelog

See [CHANGELOG](CHANGELOG.md).

## Testing

```bash
composer test
```

## Security

If you discover any security related issues, please email jean.pierre@lab123.com.br instead of using the issue tracker.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Jean Pierre](https://github.com/jeanpfs)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
