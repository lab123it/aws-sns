# Changelog

All notable changes to `aws-sns` are documented in this file.

## 2.0.0

Breaking release: the package is rebuilt for current Laravel/Lumen and the AWS SDK for PHP v3.

- Requires PHP ^8.1, `illuminate/*` ^10.0, ^11.0, ^12.60 or ^13.10 and `aws/aws-sdk-php` ^3.
  Lumen 10 and 11 and Laravel 10 to 13 are verified in real applications.
- `illuminate/mail` 10 and 11 are affected by security advisory PKSA-zwc5-qtrz-zm1n and have
  no fix; Composer blocks them until the advisory is ignored. See the README.
- A single `AwsSnsServiceProvider` now serves Laravel and Lumen. `AwsSnsLumenServiceProvider`
  stays as an alias so existing `bootstrap/app.php` registrations keep working.
- Dropped `aws/aws-sdk-php-laravel`; the SNS client is built directly with `new SnsClient()`
  (`SnsClient::factory()` no longer exists in SDK v3).
- The service provider is auto-discovered in Laravel, merges the `aws-sns` config, and
  registers the contextual `SnsClient` binding in `register()`.
- `v1.0.0` is tagged on the last pre-2.0 commit for Laravel/Lumen 5 apps (`lab123/aws-sns:^1.0`).
- Channels and exceptions are typed.
- Added tests and a GitHub Actions matrix (PHP 8.1 to 8.5, Laravel 10 to 13).
- `v1.0.0` is tagged on the last pre-2.0 commit for Laravel/Lumen 5 apps (`lab123/aws-sns:^1.0`).

## Master - 2017-09-12
- Command for composer changed to use `dev-master` version

## Master - 2017-07-17

- Fix Lumen Style config to Laravel Style
- New Service Provider for Lumen

## Master - 2017-05-07

- New default configurations to sending SMS

## Master - 2016-11-23

- initial release
