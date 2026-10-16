# Changelog

All notable changes to `aws-sns` are documented in this file.

## 2.0.0

Breaking release. The package is rebuilt for current Laravel and the AWS SDK for PHP v3.

- Requires PHP ^8.2, `illuminate/*` ^12.60 or ^13.10 and `aws/aws-sdk-php` ^3.
  Older Laravel lines are affected by security advisory PKSA-zwc5-qtrz-zm1n.
- Removed `AwsSnsLumenServiceProvider`. Lumen stops at Laravel 10; the pre-2.0 code remains available as `dev-master` history for Lumen.
- Dropped `aws/aws-sdk-php-laravel`; the SNS client is built directly with `new SnsClient()`
  (`SnsClient::factory()` no longer exists in SDK v3).
- The service provider merges the `aws-sns` config, is auto-discovered, and registers the
  contextual `SnsClient` binding in `register()`.
- Channels and exceptions are typed.
- Added tests and a GitHub Actions matrix (PHP 8.2 to 8.5).

## Master - 2017-09-12
- Command for composer changed to use `dev-master` version

## Master - 2017-07-17

- Fix Lumen Style config to Laravel Style
- New Service Provider for Lumen

## Master - 2017-05-07

- New default configurations to sending SMS

## Master - 2016-11-23

- initial release
